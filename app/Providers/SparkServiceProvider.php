<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Paddle\Cashier;
use Spark\Plan;
use Spark\Spark;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(Customer::class);
        Cashier::useSubscriptionModel(Subscription::class);
        Cashier::useSubscriptionItemModel(SubscriptionItem::class);
        Cashier::useTransactionModel(Transaction::class);

        Spark::billable(Team::class)->resolve(function (Request $request) {
            return $request->user()->currentTeam;
        });

        Spark::billable(Team::class)->authorize(function (Team $billable, Request $request) {
            return $request->user() &&
                   $request->user()->current_team_id == $billable->id &&
                   $request->user()->isA(Role::ADMIN->value);
        });

        Spark::billable(Team::class)->checkPlanEligibility(function (Team $billable, Plan $plan) {
            // if ($billable->projects > 5 && $plan->name == 'Basic') {
            //     throw ValidationException::withMessages([
            //         'plan' => 'You have too many projects for the selected plan.'
            //     ]);
            // }
        });
    }
}
