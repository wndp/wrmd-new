<?php

namespace App\Actions;

// use App\Events\NewUser;
// use App\Events\Registered;
use App\Concerns\AsAction;
use App\Enums\AccountStatus;
use App\Enums\Extension;
use App\Enums\Role;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;
use App\Support\ExtensionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\AddsTeamMembers;

class RegisterSubAccount
{
    use AsAction;

    protected $user;
    protected $request;
    protected $masterAccount;

    public function handle(User $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
        $this->masterAccount = $user->currentTeam;

        $subAccount = $this->createTeam();
        $subAccount->masterAccount()->associate($this->masterAccount);
        $subAccount->save();

        $this->cloneSettings($subAccount);
        //$this->cloneCustomFields($subAccount);
        $this->addUsers($subAccount);
        $this->activateExtensions($subAccount);

        //event(new Registered($subAccount, $user));

        return $subAccount;
    }

    public function createTeam()
    {
        return $this->user->ownedTeams()->create([
            'status' => AccountStatus::ACTIVE,
            'personal_team' => false,
            'name' => $this->request->input('name'),
            'federal_permit_number' => $this->request->input('federal_permit_number'),
            'subdivision_permit_number' => $this->request->input('subdivision_permit_number'),
            'contact_name' => $this->request->input('contact_name'),
            'country' => $this->request->input('country'),
            'address' => $this->request->input('address'),
            'city' => $this->request->input('city'),
            'subdivision' => $this->request->input('subdivision'),
            'postal_code' => $this->request->input('postal_code'),
            'phone_number' => $this->request->input('phone_number'),
            'contact_email' => $this->request->input('contact_email'),
            'notes' => $this->request->input('notes'),
        ]);
        // return Team::create($this->request->only([
        //     'name',
        //     'federal_permit_number',
        //     'subdivision_permit_number',
        //     'contact_name',
        //     'country',
        //     'address',
        //     'city',
        //     'subdivision',
        //     'postal_code',
        //     'phone_number',
        //     'contact_email',
        //     'notes',
        // ]));
    }

    /**
     * Clone settings into the subAccount.
     *
     * @return null
     */
    public function cloneSettings(Team $subAccount)
    {
        if ($this->request->boolean('clone_settings')) {
            $subAccountSettings = $subAccount->settingsStore();

            Collection::make($this->masterAccount->settingsStore()->all())->map(
                fn ($value, $key) => $subAccountSettings->set([$key => $value])
            );
        }
    }

    /**
     * Clone custom fields into the subAccount.
     *
     * @return null
     */
    public function cloneCustomFields(Team $subAccount)
    {
        if ($this->request->boolean('clone_custom_fields')) {
            $select = CustomField::selectRaw($subAccount->id.', `account_field_id`, `panel`, `location`, `type`, `label`, `options`, `is_required`, now(), now()')
                ->where('team_id', $this->masterAccount->id);

            $insertQuery = 'INSERT into custom_fields (`team_id`, `account_field_id`, `panel`, `location`, `type`, `label`, `options`, `is_required`, `updated_at`, `created_at`) '.$select->toSql();

            DB::insert($insertQuery, $select->getBindings());
        }
    }

    /**
     * Add users into the subAccount.
     *
     * @return null
     */
    public function addUsers(Team $subAccount)
    {
        // Add the currently authenticated user as an Admin.
        if ($this->request->boolean('add_current_user')) {
            $this->user->joinTeam($subAccount, Role::ADMIN);

            // $this->user->joinAccount($subAccount);
            // event(new NewUser($this->user, $subAccount));
        }

        if ($this->request->filled('users')) {
            $userIds = Arr::wrap($this->request->input('users'));

            //->teammates()

            if (! empty($userIds)) {
                $this->masterAccount
                    ->allUsers()
                    ->load('roles')
                    ->whereIn('id', $userIds)
                    ->each(function ($user) use ($subAccount) {
                        $user->joinTeam($subAccount, Role::tryFrom(
                            $user->roleOn($this->masterAccount)->name
                        ));

                        //$accountUser->user->joinAccount($subAccount, $accountUser->user->roleOn($this->masterAccount)->name);
                        //event(new NewUser($accountUser->user, $subAccount));
                    });
            }
        }
    }

    /**
     * Activate extensions on the subAccount.
     *
     * @return null
     */
    public function activateExtensions(Team $subAccount)
    {
        if ($this->request->boolean('clone_extensions')) {
            ExtensionManager::getActivated($this->masterAccount)
                ->each(fn ($extension) => ExtensionManager::activate(
                    $subAccount,
                    Extension::tryFrom($extension->extension),
                    activateDependents: false
                ));
        }
    }
}
