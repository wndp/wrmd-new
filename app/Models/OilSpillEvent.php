<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\SettingKey;
use App\Support\Wrmd;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OilSpillEvent extends Team
{
    use HasFactory;

    /**
     * Possible Wildlife Recovery application spill IDs.
     *
     * @var array
     */
    public const SPILL_IDS = [
        'SPILL-1',
        'SPILL-2',
        'SPILL-3',
        'SPILL-4',
        'SPILL-5',
        'SPILL-6',
        'SPILL-TEST',
    ];

    protected $table = 'teams';

    /**
     * Get the list of active spill events.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function activeSpillEvents()
    {
        return self::where('status', AccountStatus::ACTIVE)->whereIn('id', function ($query) {
            $query->select('team_id')
                ->from('settings')
                ->where('key', SettingKey::OSPR_SPILL_ID->value)
                ->where('value', 'like', json_encode('SPILL-%'));
        })
        ->get();
    }

    /**
     * Get an array of unused spill IDs.
     *
     * @param  bool $preserveCurrentAccount
     * @return array
     */
    public static function filterAvailableSpillIds($preserveCurrentAccount = false)
    {
        $usedSpillIds = Setting::where('key', SettingKey::OSPR_SPILL_ID->value)->pluck('value')->toArray();

        if ($preserveCurrentAccount && ($key = array_search(Wrmd::settings(SettingKey::OSPR_SPILL_ID), $usedSpillIds)) !== false) {
            unset($usedSpillIds[$key]);
        }

        return array_values(array_diff(self::SPILL_IDS, $usedSpillIds));
    }
}
