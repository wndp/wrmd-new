<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Mail\ReportEmail;
use App\Rules\CommaSeparatedEmails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReportEmailController extends Controller
{
    /**
     * Generate the requested report.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ReportRequest $request, string $namespace)
    {
        $request->validate([
            'to' => ['required', new CommaSeparatedEmails()],
            'bcc_me' => 'nullable|boolean',
            'subject' => 'required',
            'body' => 'required',
        ]);

        $report = $request->report($namespace);
        $mail = Mail::to(preg_split('/[,\s]+/', $request->to));

        if ($request->boolean('bcc_me')) {
            $mail->bcc(Auth::user()->email, Auth::user()->name);
        }

        $mail->send(new ReportEmail(
            Auth::user(),
            $report,
            $request->subject,
            $request->body
        ));

        return back()
            ->with('notification.heading', __('Report Emailed!'))
            ->with('notification.text', __(
                'Your :reportTitle report has been emailed.',
                [
                    'reportTitle' => $report->title(), ]
            ));
    }
}
