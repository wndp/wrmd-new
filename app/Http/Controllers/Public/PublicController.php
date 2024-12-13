<?php

namespace App\Http\Controllers\Public;

use App\Enums\Attribute;
use App\Http\Controllers\Controller;
use App\Importing\FacilitatesImporting;
use App\Models\Testimonial;
use App\Repositories\RecentNews;
use App\Support\ExtensionManager;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    use FacilitatesImporting;

    public function testimonials()
    {
        $testimonials = Cache::remember('testimonials', Carbon::now()->addHours(2), function () {
            return Testimonial::with('team')->limit(10)->get()->shuffle();
        });

        return Inertia::render('Public/Testimonials', compact('testimonials'));
    }

    public function features(): Response
    {
        $extensions = Arr::map(ExtensionManager::getPublic(), fn ($extension) => [
            ...$extension->toArray(),
        ]);

        $standardExtensions = array_values(Arr::where($extensions, fn ($extension) => !$extension['pro']));
        $proExtensions = array_values(Arr::where($extensions, fn ($extension) => $extension['pro']));

        return Inertia::render('Public/Features', compact('standardExtensions', 'proExtensions'));
    }

    public function new(): Response
    {
        $posts = RecentNews::collect();

        return Inertia::render('Public/WhatsNew', compact('posts'));
    }

    public function importing()
    {
        $translatableFields = collect([
            'exams.sex_id',
            'exams.weight_unit_id',
            'exams.body_condition_id',
            'exams.attitude_id',
            'exams.dehydration_id',
            'exams.mucous_membrane_color_id',
            'exams.mucous_membrane_texture_id',
            'patients.disposition_id',
            'patients.transfer_type_id',
            'patients.release_type_id',
            'patients.is_carcass_saved',
        ])
        ->transform(fn ($attribute) => Attribute::from($attribute))
        ->filter(fn ($attribute) => $attribute->hasAttributeOptions())
        ->transform(fn ($attribute) => [
            'label' => $attribute->label(),
            'options' => array_values($attribute->options()->first() ?? []),
        ]);

        session()->put('import.whatImporting', 'patients');

        $importableEntities = $this->importableEntities()->transform(function ($entity) {
            return [
                'label' => $entity,
                'slug' => Str::slug($entity),
            ];
        });

        $filteredRequiredFields = collect($this->filteredRequiredFields())->transform(
            fn ($attribute) =>
            Attribute::from($attribute)->label()
        );

        return Inertia::render('Public/Importing', compact(
            'importableEntities',
            'filteredRequiredFields',
            'translatableFields'
        ));
    }

    public function agencies()
    {
        $annualReports  = [];

        return Inertia::render('Public/Agencies', compact('annualReports'));
    }
}
