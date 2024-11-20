<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Accounts\Account;
use App\Domain\Accounts\Testimonial;
use App\Domain\Options;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountsTestimonialsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $testimonials = Testimonial::with('account')
            ->when(
                $request->get('search'),
                fn ($query, $search) => $query->where('name', 'like', "%$search%")->orWhere('text', 'like', "%$search%")
            )
            ->paginate();

        return Inertia::render('Admin/Testimonials/Index', compact('testimonials'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function create(): Response
    {
        $accounts = Options::arrayToSelectable(
            Account::where('status', 'Active')->get()->pluck('organization', 'id')->sort()->toArray()
        );

        return Inertia::render('Admin/Testimonials/Create', compact('accounts'));
    }

    /**
     * Store a new testimonial in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Testimonial::create($request->validate([
            'name' => 'required',
            'text' => 'required',
            'account_id' => 'required',
        ]));

        return redirect()->route('admin.testimonials.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Testimonial $testimonial): Response
    {
        $accounts = Options::arrayToSelectable(
            Account::where('status', 'Active')->get()->pluck('organization', 'id')->sort()->toArray()
        );

        return Inertia::render('Admin/Testimonials/Edit', compact('testimonial', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($request->validate([
            'name' => 'required',
            'text' => 'required',
            'account_id' => 'required',
        ]));

        return redirect()->route('admin.testimonials.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('flash.notificationHeading', 'I Hope You Meant That!')
            ->with('flash.notification', "{$testimonial->name}'s testimonial was deleted.");
    }
}
