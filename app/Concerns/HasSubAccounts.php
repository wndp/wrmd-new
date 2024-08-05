<?php

namespace App\Concerns;

use App\Models\Team;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasSubAccounts
{
    /**
     * Get a master account's sub-accounts.
     */
    public function subAccounts(): HasMany
    {
        return $this->hasMany(Team::class, 'master_account_id');
    }

    /**
     * Get a sub-account's master account.
     */
    public function masterAccount(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'master_account_id');
    }

    /**
     * Determine if the account is a sub-account.
     */
    public function isSubAccount(): bool
    {
        return ! is_null($this->masterAccount);
    }

    /**
     * Determine if the master account has the given sub-account.
     */
    public function hasSubAccount(Account $account): bool
    {
        return $this->fresh()->subAccounts->contains($account);
    }

    /**
     * Assign the account the provided master account.
     */
    public function assignToMasterAccount(Account $masterAccount): static
    {
        $this->masterAccount()->associate($masterAccount)->save();

        return $this;
    }

    /**
     * Remove the account from its master account.
     */
    public function removeFromMasterAccount(): static
    {
        $this->masterAccount()->dissociate()->save();

        return $this;
    }
}
