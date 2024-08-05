<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Accounts\Account;
use App\Domain\Database\RecordNotOwnedResponse;
use App\Events\AccountUpdated;
use App\Extensions\Extension;
use App\Extensions\ExtensionManager;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccountExtensionsController extends Controller
{
    /**
     * @var ExtensionManager
     */
    protected $extensionManager;

    /**
     * Constructor.
     */
    public function __construct(ExtensionManager $extensionManager)
    {
        $this->extensionManager = $extensionManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activated = $this->extensionManager->getActivated(Auth::user()->currentAccount);
        $extensions = $this->extensionManager
            ->getPublic()
            ->transform(function ($extension) use ($activated) {
                $extension->is_activated = $activated->contains($extension);

                return $extension;
            });

        return Inertia::render('Settings/Extensions', compact('extensions'));
    }

    /**
     * Activate an extension.
     */
    public function store(Request $request, string $namespace, Account $account = null): RedirectResponse
    {
        if ($account instanceof Account) {
            abort_unless(
                Auth::user()->can('manageAccounts') || Auth::user()->currentAccount->hasSubAccount($account),
                new RecordNotOwnedResponse()
            );
        }

        $extension = $this->getExtension($namespace);
        $account = $account ?: Auth::user()->currentAccount;

        $this->extensionManager->activate($account, $extension->id);

        event(new AccountUpdated($account));

        return redirect()
            ->back()
            ->with('flash.notificationHeading', 'Extension Activated!')
            ->with('flash.notification', "$extension->name is activated and ready to use.");
    }

    /**
     * Deactivate an extension.
     */
    public function destroy(string $namespace, Account $account = null): RedirectResponse
    {
        if ($account instanceof Account) {
            abort_unless(
                Auth::user()->can('manageAccounts') || Auth::user()->currentAccount->hasSubAccount($account),
                new RecordNotOwnedResponse()
            );
        }

        try {
            $extension = $this->getExtension($namespace);
            $account = $account ?: Auth::user()->currentAccount;

            $this->extensionManager->deactivate($account, $extension->id);
            event(new AccountUpdated($account));

            return redirect()
                ->back()
                ->with('flash.notificationHeading', 'Extension Deactivated')
                ->with('flash.notification', "$extension->name is deactivated.");
        } catch (\DomainException $e) {
            return redirect()
                ->back()
                ->with('flash.notificationHeading', 'Oops!')
                ->with('flash.notification', $e->getMessage())
                ->with('flash.style', 'danger');
        }
    }

    /**
     * Get an extension or abort with a 404.
     */
    public function getExtension(string $namespace): Extension
    {
        try {
            return $this->extensionManager->findByNamespace($namespace);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }
}
