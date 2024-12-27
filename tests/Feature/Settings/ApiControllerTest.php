<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class ApiControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_api_settings_page(): void
    {
        $this->get(route('api.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_api_settings_page(): void
    {
        $me = $this->createTeamUser(role: Role::USER);
        $this->actingAs($me->user)->get(route('api.index'))->assertForbidden();
    }

    public function test_it_displays_the_api_settings_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('api.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/Api')
                    ->has('tokens');
            });
    }

    public function test_a_token_name_is_required_to_store_a_new_api_token(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('api.store'))
            ->assertInvalid(['token_name' => 'The token name field is required.']);
    }

    public function test_an_array_of_token_abilities_is_required_to_store_a_new_api_token(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('api.store'))
            ->assertInvalid(['token_abilities' => 'The token abilities field is required.']);

        $this->actingAs($me->user)->post(route('api.store'), ['token_abilities' => 'test'])
            ->assertInvalid(['token_abilities' => 'The token abilities field must be an array.']);
    }

    public function test_a_new_api_token_is_saved_to_storage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('api.store'), [
            'token_name' => 'Foo Bar',
            'token_abilities' => [
                'donations:view',
            ],
        ])
            ->assertRedirect(route('api.index'))
            ->assertSessionHas('flash.token');

        $apiUser = User::apiUserFor($me->team);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => Relation::getMorphAlias(User::class),
            'tokenable_id' => $apiUser->id,
            'name' => 'Foo Bar',
            'abilities' => json_encode(['donations:view']),
        ]);

        $this->assertTrue(
            $apiUser->withAccessToken($apiUser->tokens->first())->tokenCan('donations:view')
        );
    }

    public function test_it_validates_ownership_of_the_api_token_before_deleting(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $you = $this->createTeamUser();
        $apiUser = User::apiUserFor($you->team);
        $token = $apiUser->createToken('Foo Bar');

        $this->actingAs($me->user)->delete(route('api.destroy', $token->accessToken->id))->assertOwnershipValidationError();
    }

    public function test_an_api_token_is_deleted_from_storage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $apiUser = User::apiUserFor($me->team);
        $token = $apiUser->createToken('Foo Bar');

        $this->actingAs($me->user)
            ->delete(route('api.destroy', $token->accessToken->id))
            ->assertRedirect(route('api.index'));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => Relation::getMorphAlias(User::class),
            'tokenable_id' => $apiUser->id,
            'name' => 'Foo Bar',
        ]);
    }
}
