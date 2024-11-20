<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Extensions\ExtensionManager;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AccountsExtensionsController extends Controller
{
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
    public function __invoke(Account $account)
    {
        $activated = $this->extensionManager->getActivated($account);
        $extensions = $this->extensionManager
            ->getAll()
            ->transform(function ($extension) use ($activated) {
                $extension->is_activated = $activated->contains($extension);

                return $extension;
            });

        return Inertia::render('Admin/Accounts/Extensions', compact('account', 'extensions'));
    }
}
