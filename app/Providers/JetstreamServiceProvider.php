<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use App\Options\LocaleOptions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Spatie\Honeypot\Honeypot;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);

        Fortify::loginView(function () {
            return Inertia::render('Auth/Login', [
                'canResetPassword' => Route::has('password.request'),
                'status' => session('status'),
                'honeypot' => app(Honeypot::class),
            ]);
        });

        Fortify::registerView(function () {
            return Inertia::render('Auth/Register', [
                'options' => app(LocaleOptions::class)->toArray(),
                'honeypot' => app(Honeypot::class),
            ]);
        });
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        //Jetstream::defaultApiTokenPermissions(['read']);

        // Jetstream::role('admin', 'Administrator', [
        //     'create',
        //     'read',
        //     'update',
        //     'delete',
        // ])->description('Administrator users can perform any action.');

        // Jetstream::role('editor', 'Editor', [
        //     'read',
        //     'create',
        //     'update',
        // ])->description('Editor users have the ability to read, create, and update.');
    }
}
