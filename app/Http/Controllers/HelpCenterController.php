<?php

namespace App\Http\Controllers;

use App\Mail\HelpCenterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HelpCenterController extends Controller
{
    /**
     * Show the help center form.
     */
    public function index()
    {
        return view('help-center');
    }

    /**
     * Handle form submission and send the support email.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'email'      => 'required|email|max:255',
            'issue'      => 'required|string|max:100',
            'details'    => 'required|string|max:5000',
        ]);

        Mail::to(env('SUPPORT_EMAIL', config('mail.from.address')))
            ->send(new HelpCenterMail(
                firstName: $validated['first_name'],
                userEmail: $validated['email'],
                issueType: $validated['issue'],
                details:   $validated['details'],
            ));

        return back()->with('success', 'Your support request has been sent! We\'ll get back to you as soon as possible.');
    }
}
