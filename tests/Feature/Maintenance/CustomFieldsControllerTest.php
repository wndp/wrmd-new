<?php

namespace Tests\Feature\Maintenance;

use App\Actions\CustomFieldBuilder;
use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\AttributeOption;
use App\Models\AttributeOptionUiBehavior as AttributeOptionUiBehaviorModel;
use App\Models\CustomField;
use App\Models\CustomValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class CustomFieldsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    private function createAttributeOptions()
    {
        $customFieldGroupIsPatientId = $this->createUiBehavior(
            AttributeOptionName::CUSTOM_FIELD_GROUPS,
            AttributeOptionUiBehavior::CUSTOM_FIELD_GROUP_IS_PATIENT
        )->attribute_option_id;

        $locationId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::CUSTOM_FIELD_LOCATIONS
        ])->id;

        $panelId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::CUSTOM_FIELD_PATIENT_PANELS
        ])->id;

        $typeId = AttributeOption::factory()->create([
            'name' => AttributeOptionName::CUSTOM_FIELD_TYPES
        ])->id;

        return [
            $customFieldGroupIsPatientId,
            $locationId,
            $panelId,
            $typeId
        ];
    }

    #[Test]
    public function unAuthenticatedUsersCantAccessCustomFields(): void
    {
        $this->get(route('maintenance.custom_fields.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessCustomFields(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('maintenance.custom_fields.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheCustomFieldsIndexPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $response = $this->actingAs($me->user)->get(route('maintenance.custom_fields.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Maintenance/CustomFields/Index')
                    ->has('fields')
                    ->where('numAllowedFields', CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS)
            );
    }

    #[Test]
    public function itDisplaysTheCustomFieldsCreatePage(): void
    {
        AttributeOption::factory()->create([
            'name' => AttributeOptionName::CUSTOM_FIELD_TYPES,
        ]);

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $response = $this->actingAs($me->user)->get(route('maintenance.custom_fields.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Maintenance/CustomFields/Create')
                    ->hasOption('customFieldTypesOptions');
            });
    }

    #[Test]
    public function theCustomFieldsCreatePageFailsToDisplayIfTheNumberOfAllowedFieldsIsExceeded(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $numberOfAllowedFields = CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS;
        CustomField::factory()->count($numberOfAllowedFields)->create(['team_id' => $me->team->id]);

        $response = $this->actingAs($me->user)->get(route('maintenance.custom_fields.create'))
            ->assertStatus(302)
            ->assertHasNotificationMessage(
                "You already have {$numberOfAllowedFields} custom fields created!"
            );
    }

    #[Test]
    public function aLabelIsRequiredToCreateACustomField(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'))
            ->assertInvalid(['label' => 'The label field is required.']);
    }

    #[Test]
    public function aValidGroupIsRequiredToCreateACustomField(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'))
            ->assertInvalid(['group_id' => 'The group id field is required.']);

        // TODO: make this work
        // $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'), ['group_id' => 123])
        //     ->assertInvalid(['group_id' => 'The selected group is invalid.']);
    }

    #[Test]
    public function aValidLocationIsRequiredToCreateACustomField(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'))
            ->assertInvalid(['location_id' => 'The location id field is required.']);

        // TODO: make this work
        // $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'), ['location_id' => 'foo'])
        //     ->assertInvalid(['location_id' => 'The selected location is invalid.']);
    }

    #[Test]
    public function aValidPanelIsRequiredToCreateACustomField(): void
    {
        $customFieldGroupIsPatientId = $this->createUiBehavior(
            AttributeOptionName::CUSTOM_FIELD_GROUPS,
            AttributeOptionUiBehavior::CUSTOM_FIELD_GROUP_IS_PATIENT
        )->attribute_option_id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'), ['group_id' => $customFieldGroupIsPatientId])
            ->assertInvalid(['panel_id' => 'The panel id field is required.']);

        // TODO: make this work
        // $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'), ['group' => $customFieldGroupIsPatientId, 'panel_id' => 'foo'])
        //     ->assertInvalid(['panel_id' => 'The selected panel is invalid.']);
    }

    #[Test]
    public function aValidTypeIsRequiredToCreateACustomField(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'))
            ->assertInvalid(['type_id' => 'The type id field is required.']);

        // TODO: make this work
        // $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'), ['type_id' => 'foo'])
        //     ->assertInvalid(['type_id' => 'The selected type is invalid.']);
    }

    #[Test]
    public function aValidOptionsIsRequiredToCreateACustomField(): void
    {
        $customFieldTypesRequiresOptionsId = $this->createUiBehavior(
            AttributeOptionName::CUSTOM_FIELD_TYPES,
            AttributeOptionUiBehavior::CUSTOM_FIELD_TYPES_REQUIRES_OPTIONS
        )->attribute_option_id;

        AttributeOptionUiBehaviorModel::factory()->create([
            'attribute_option_id' => $customFieldTypesRequiresOptionsId,
            'behavior' => AttributeOptionUiBehavior::CUSTOM_FIELD_TYPES_REQUIRES_OPTIONS->value,
        ]);

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'), ['type_id' => $customFieldTypesRequiresOptionsId])
            ->assertInvalid(['options' => 'The options field is required.']);

        // $this->actingAs($me->user)->post(route('maintenance.custom_fields.store'), ['type' => 'checkbox-group'])
        //     ->assertInvalid(['options' => 'The options field is required.']);
    }

    #[Test]
    public function aNewCustomFieldIsSavedToStorage(): void
    {
        [
            $customFieldGroupIsPatientId,
            $locationId,
            $panelId,
            $typeId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $this->actingAs($me->user)->post(route('maintenance.custom_fields.store', [
            'label' => 'Foo Bar',
            'group_id' => $customFieldGroupIsPatientId,
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'type_id' => $typeId,
            'is_required' => 1,
            'options' => 'Foo, Bar',
        ]))
            ->assertRedirect(route('maintenance.custom_fields.index'));

        $this->assertDatabaseHas('custom_fields', [
            'team_id' => $me->team->id,
            'team_field_id' => 1,
            'label' => 'Foo Bar',
            'group_id' => $customFieldGroupIsPatientId,
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'type_id' => $typeId,
            'is_required' => 1,
            'options' => json_encode(['Foo', 'Bar']),
        ]);
    }

    #[Test]
    public function aNewCustomFieldIsSavedToStorageInAVacantDeletedSpot(): void
    {
        [
            $customFieldGroupIsPatientId,
            $locationId,
            $panelId,
            $typeId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        CustomField::factory()->create(['team_id' => $me->team->id, 'team_field_id' => 1]);
        CustomField::factory()->create(['team_id' => $me->team->id, 'team_field_id' => 3]);

        $this->actingAs($me->user)->post(route('maintenance.custom_fields.store', [
            'label' => 'Foo Bar',
            'group_id' => $customFieldGroupIsPatientId,
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'type_id' => $typeId,
            'is_required' => 0,
        ]))
            ->assertRedirect(route('maintenance.custom_fields.index'));

        $this->assertDatabaseHas('custom_fields', [
            'team_id' => $me->team->id,
            'team_field_id' => 2,
            'label' => 'Foo Bar',
            'group_id' => $customFieldGroupIsPatientId,
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'type_id' => $typeId,
            'is_required' => 0,
        ]);
    }

    #[Test]
    public function itDisplaysThePageToEditACustomField(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $customField = CustomField::factory()->create(['team_id' => $me->team->id]);

        $response = $this->actingAs($me->user)->get(route('maintenance.custom_fields.edit', $customField))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Maintenance/CustomFields/Edit')
                    ->has('customField')
                    ->where('customField.id', $customField->id)
            );
    }

    #[Test]
    public function aLabelIsRequiredToUpdateACustomFields(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $customField = CustomField::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField))
            ->assertInvalid(['label' => 'The label field is required.']);
    }

    #[Test]
    public function aValidLocationIsRequiredToUpdateACustomFields(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $customField = CustomField::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField))
            ->assertInvalid(['location_id' => 'The location id field is required.']);

        // $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField), ['location' => 'foo'])
        //     ->assertInvalid(['location_id' => 'The selected location is invalid.']);
    }

    #[Test]
    public function aValidPanelIsRequiredToUpdateACustomFields(): void
    {
        [
            $customFieldGroupIsPatientId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $customField = CustomField::factory()->create(['team_id' => $me->team->id, 'group_id' => $customFieldGroupIsPatientId]);

        $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField))
            ->assertInvalid(['panel_id' => 'The panel id field is required.']);

        // $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField), ['panel' => 'foo'])
        //     ->assertInvalid(['panel_id' => 'The selected panel is invalid.']);
    }

    #[Test]
    public function aValidOptionsIsRequiredToUpdateACustomFields(): void
    {
        $customFieldTypesRequiresOptionsId = $this->createUiBehavior(
            AttributeOptionName::CUSTOM_FIELD_TYPES,
            AttributeOptionUiBehavior::CUSTOM_FIELD_TYPES_REQUIRES_OPTIONS
        )->attribute_option_id;

        AttributeOptionUiBehaviorModel::factory()->create([
            'attribute_option_id' => $customFieldTypesRequiresOptionsId,
            'behavior' => AttributeOptionUiBehavior::CUSTOM_FIELD_TYPES_REQUIRES_OPTIONS->value,
        ]);

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $customField = CustomField::factory()->create([
            'team_id' => $me->team->id,
            'type_id' => $customFieldTypesRequiresOptionsId
        ]);

        $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField))
            ->assertInvalid(['options' => 'The options field is required.']);
    }

    #[Test]
    public function itValidatesOwnershipOfACustomFieldBeforeUpdating(): void
    {
        [
            $customFieldGroupIsPatientId,
            $locationId,
            $panelId,
            $typeId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $customField = CustomField::factory()->create();

        $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField), [
            'label' => 'Foo Bar',
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'is_required' => 1,
            'options' => 'Foo, Bar',
        ])
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function aCustomFieldIsUpdatedToStorage(): void
    {
        [
            $customFieldGroupIsPatientId,
            $locationId,
            $panelId,
            $typeId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $customField = CustomField::factory()->create([
            'team_id' => $me->team->id,
            'group_id' => $customFieldGroupIsPatientId,
            'type_id' => $typeId,
        ]);

        $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField), [
            'label' => 'Foo Bar',
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'group_id' => 999, // The group_id can not be updated
            'type_id' => 999, // The type_id can not be updated
            'is_required' => 1,
            'options' => 'Foo, Bar',
        ])
            ->assertRedirect(route('maintenance.custom_fields.index'));

        $this->assertDatabaseHas('custom_fields', [
            'id' => $customField->id,
            'team_id' => $me->team->id,
            'label' => 'Foo Bar',
            'group_id' => $customFieldGroupIsPatientId,
            'type_id' => $typeId,
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'is_required' => 1,
            'options' => json_encode(['Foo', 'Bar']),
        ]);
    }

    #[Test]
    public function aCustomFieldWithOptionsCanBeSavedInCommaSeperatedFormat()
    {
        [
            $customFieldGroupIsPatientId,
            $locationId,
            $panelId,
            $typeId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $customField = CustomField::factory()->create([
            'team_id' => $me->team->id,
            'group_id' => $customFieldGroupIsPatientId,
            'type_id' => $typeId,
        ]);

        $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField), [
            'label' => 'Foo Bar',
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'is_required' => 1,
            'options' => 'one, two, three',
        ]);

        $this->assertDatabaseHas('custom_fields', [
            'id' => $customField->id,
            'options' => json_encode(['one', 'two', 'three']),
        ]);
    }

    #[Test]
    public function aCustomFieldWithOptionsCanBeSavedInOnePerLineFormat()
    {
        [
            $customFieldGroupIsPatientId,
            $locationId,
            $panelId,
            $typeId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);

        $customField = CustomField::factory()->create([
            'team_id' => $me->team->id,
            'group_id' => $customFieldGroupIsPatientId,
            'type_id' => $typeId,
        ]);

        $this->actingAs($me->user)->put(route('maintenance.custom_fields.update', $customField), [
            'label' => 'Foo Bar',
            'location_id' => $locationId,
            'panel_id' => $panelId,
            'is_required' => 1,
            'options' => "one\ntwo\nthree",
        ]);

        $this->assertDatabaseHas('custom_fields', [
            'id' => $customField->id,
            'options' => json_encode(['one', 'two', 'three']),
        ]);
    }

    #[Test]
    public function itValidatesOwnershipOfACustomFieldsBeforeDeleting(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $customField = CustomField::factory()->create();

        $this->actingAs($me->user)->delete(route('maintenance.custom_fields.destroy', $customField))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function aCustomFieldCanBeDeleted(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CUSTOM_FIELDS->value);
        $admission = $this->createCase($me->team);

        $customField = CustomField::factory()->create(['team_id' => $me->team->id]);
        $customValue = CustomValue::factory()->create([
            'team_id' => $me->team->id,
            'customable_type' => $admission->patient->getMorphClass(),
            'customable_id' => $admission->patient->id,
            "custom_field_{$customField->team_field_id}" => 'foo bar',
        ]);

        $this->actingAs($me->user)->delete(route('maintenance.custom_fields.destroy', $customField))
            ->assertRedirect(route('maintenance.custom_fields.index'));

        $this->assertDatabaseMissing('custom_fields', [
            'id' => $customField->id,
        ]);

        $this->assertDatabaseHas('custom_values', [
            'id' => $customValue->id,
            'team_id' => $me->team->id,
            'customable_type' => $admission->patient->getMorphClass(),
            'customable_id' => $admission->patient->id,
            "custom_field_{$customField->team_field_id}" => null,
        ]);
    }
}
