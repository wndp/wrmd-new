<?php

namespace App\Actions;

// use App\Events\NewUser;
// use App\Events\Registered;
use App\Concerns\AsAction;
use App\Enums\AccountStatus;
use App\Enums\Extension;
use App\Enums\Role;
use App\Enums\SettingKey;
use App\Models\CustomField;
use App\Models\Team;
use App\Models\User;
use App\Support\ExtensionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        $this->cloneCustomFields($subAccount);
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
        $masterAccountSettings = $this->masterAccount->settingsStore();
        $subAccountSettings = $subAccount->settingsStore();

        if ($this->request->boolean('clone_settings')) {
            Collection::make($masterAccountSettings->all())->map(
                fn ($value, $key) => $subAccountSettings->set([$key => $value])
            );
        } else {
            $subAccountSettings->set([
                SettingKey::TIMEZONE->value => $masterAccountSettings->get(SettingKey::TIMEZONE, 'America/Los_Angeles'),
                SettingKey::LANGUAGE->value => $masterAccountSettings->get(SettingKey::LANGUAGE, 'en'),
            ]);
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
            CustomField::where('team_id', $this->masterAccount->id)->get()->each(
                fn ($customField) =>
                CustomField::create(array_merge(
                    Arr::except($customField->toArray(), ['id', 'team_id', 'created_at', 'updated_at']),
                    [
                        'team_id' => $subAccount->id
                    ]
                ))
            );
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
                ->each(fn ($teamExtension) => ExtensionManager::activate(
                    $subAccount,
                    $teamExtension->extension, //Extension::tryFrom(dd($extension->extension)),
                    activateDependents: false
                ));
        }
    }
}
