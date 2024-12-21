<?php

namespace App\Http\Controllers\Maintenance;

use App\Enums\SettingKey;
use App\Events\AccountUpdated;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use League\Flysystem\FileNotFoundException;

class PaperformsTemplatesController extends Controller
{
    /**
     * Display the paper form template settings index page.
     */
    public function index()
    {
        $templates = Wrmd::settings(SettingKey::PAPER_FORM_TEMPLATES, []);

        return Inertia::render('Maintenance/PaperForms/Index', compact('templates'));
    }

    /**
     * Store paper form template in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'template' => 'required|file|mimes:pdf|max:5000',
        ]);

        $paperformsTemplates = Wrmd::settings(SettingKey::PAPER_FORM_TEMPLATES, []);

        array_push($paperformsTemplates, [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'path' => $request->template->store('report-templates/'.Auth::user()->current_team_id, 's3'),
            'created_at' => Carbon::now(Wrmd::settings(SettingKey::TIMEZONE))->toDayDateTimeString(),
        ]);

        Wrmd::settings()->set(SettingKey::PAPER_FORM_TEMPLATES, $paperformsTemplates);

        //event(new AccountUpdated(auth()->user()->currentAccount));

        return redirect()->route('maintenance.paper_forms.index');
    }

    /**
     * Delete the specified template from storage.
     *
     * @param  string  $slug
     */
    public function destroy(Request $request, $slug)
    {
        $paperformsTemplates = collect(Wrmd::settings(SettingKey::PAPER_FORM_TEMPLATES, []));

        $key = $paperformsTemplates->search(function ($template) use ($slug) {
            return $template['slug'] === $slug;
        });

        if ($key !== false) {
            $template = $paperformsTemplates[$key];

            try {
                Storage::delete($template['path']);
            } catch (FileNotFoundException $e) {
            }

            $paperformsTemplates->offsetUnset($key);

            //Wrmd::settings(SettingKey::PAPER_FORM_TEMPLATES, [])

            if ($paperformsTemplates->isEmpty()) {
                Wrmd::settings()->delete(SettingKey::PAPER_FORM_TEMPLATES);
            } else {
                Wrmd::settings()->set(SettingKey::PAPER_FORM_TEMPLATES, $paperformsTemplates->values()->toArray());
            }

            //event(new AccountUpdated(auth()->user()->currentAccount));
            //flash()->success("{$template['name']} deleted.");
        }

        return redirect()->route('maintenance.paper_forms.index');
    }
}
