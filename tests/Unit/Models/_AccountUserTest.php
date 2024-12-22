<?php

namespace Tests\Unit\Users;

use App\Domain\Accounts\Account;
use App\Domain\Users\CanJoinAccounts;
use App\Domain\Users\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AccountUserTest extends TestCase
{
    //use CanJoinAccounts;

    public function test_an_account_user_has_belongs_to_a_user(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();

        $accountUser = $user->joinAccount($account);

        $this->assertTrue($accountUser->user->is($user));
    }

    public function test_an_account_user_has_belongs_to_an_account(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();

        $accountUser = $user->joinAccount($account);

        $this->assertTrue($accountUser->account->is($account));
    }

    /** @test */
    // public function it_determines_if_the_user_has_a_role_in_their_current_account()
    // {
    //     $user1 = User::factory()->create();
    //     $user2 = User::factory()->create();
    //     $account = Account::factory()->create();

    //     $user1->joinAccount($account, 'Viewer');
    //     $user2->joinAccount($account, 'Admin');

    //     // has a specific role
    //     $result = $user1->hasRoleInCurrentAccount('Viewer');
    //     $this->assertTrue($result);

    //     // has a specific role or a greater role
    //     $result = $user2->hasRoleInCurrentAccount('User', true);
    //     $this->assertTrue($result);
    // }

    public function test_re_assign_a_user_deleted_from_single_account(): void
    {
        // Given that a user exists in one account
        $account = Account::factory()->create();

        $user = User::factory()->create([
            'email' => 'foo@example.com',
            'parent_account_id' => $account->id,
        ]);

        // !! we don't add the user to account to emulate the user deletion

        // When I reassign the user
        $user->reAssignParentAccount($account);

        // Then they should be deleted
        $this->assertDatabaseMissing('users', ['email' => 'foo@example.com']);
    }

    public function test_re_assign_a_user_deleted_from_foreign_account(): void
    {
        // Given that a user exists in multiple accounts and is deleted from it's parent account
        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        $user = User::factory()->create([
            'email' => 'foo@example.com',
            'parent_account_id' => $account1->id,
        ]);

        // !! we don't add the user to account 2 to emulate the user deletion from that account
        $user->joinAccount($account1);

        // When I reassign the user
        $user->reAssignParentAccount($account2);

        // Then their parent id should remain the same
        $user = User::where('email', 'foo@example.com')->first();

        $this->assertEquals($account1->id, $user->parent_account_id);
    }

    public function test_re_assign_a_user_deleted_from_parent_account(): void
    {
        // Given that a user exists in multiple accounts and is deleted from it's parent account
        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        $user = User::factory()->create([
            'email' => 'foo@example.com',
            'parent_account_id' => $account1->id,
        ]);

        // !! we don't add the user to account 1 to emulate the user deletion from that account
        $user->joinAccount($account2);

        // When I reassign the user
        $user->reAssignParentAccount($account1);

        // Then their parent id should update to account 2
        $user = User::where('email', 'foo@example.com')->first();

        $this->assertEquals($account2->id, $user->parent_account_id);
    }
}
