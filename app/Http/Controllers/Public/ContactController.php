<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Honeypot\Honeypot;

class ContactController extends Controller
{
    public function create(Honeypot $honeypot): Response
    {
        return Inertia::render('Public/Contact', [
            'honeypot' => $honeypot,
            'status' => session('status'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'organization' => 'nullable',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        Mail::to('support@wildneighborsdp.org')->send(new ContactEmail($data));

        return back()->with(
            'status',
            'Thanks for reaching out to us. Your message has been sent and you should here back from us shortly.'
        );
    }
}
