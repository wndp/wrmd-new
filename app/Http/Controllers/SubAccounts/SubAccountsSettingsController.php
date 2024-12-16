<?php

namespace App\Http\Controllers\SubAccounts;

use App\Enums\SettingKey;
use App\Events\AccountUpdated;
use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SubAccountsSettingsController extends Controller
{
    public function edit(Team $subAccount): Response
    {
        abort_unless(
            Auth::user()->currentTeam->hasSubAccount($subAccount),
            new RecordNotOwned
        );

        $store = $subAccount->settingsStore();

        $subAccountSettings = [
            'subAccountAllowManageSettings' => (bool) $store->get(SettingKey::SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS, true),
            'subAccountAllowTransferPatients' => (bool) $store->get(SettingKey::SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS, true),
        ];

        $settings = Setting::where('team_id', $subAccount->id)->get()->transform(fn ($setting) => [
            'label' => SettingKey::tryFrom($setting->key)->label(),
            'value' => $setting->value,
            'updated_at_for_humans' => $setting->updated_at->translatedFormat(config('wrmd.date_time_format')),
        ]);

        return Inertia::render('SubAccounts/Settings', compact('subAccount', 'subAccountSettings', 'settings'));
    }

    public function update(Request $request, Team $subAccount): RedirectResponse
    {
        abort_unless(
            Auth::user()->currentTeam->hasSubAccount($subAccount),
            new RecordNotOwned
        );

        $subAccount->settingsStore()->set([
            SettingKey::SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS->value => request()->input('sub_account_allow_manage_settings'),
            SettingKey::SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS->value => request()->input('sub_account_allow_transfer_patients'),
        ]);

        //event(new AccountUpdated($subAccount));

        return redirect()->route('sub_accounts.settings.edit', [$subAccount->id]);
    }
}
