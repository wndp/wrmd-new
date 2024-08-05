<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Importing\FacilitatesImporting;
use App\Models\Testimonial;
use App\Repositories\RecentNews;
use App\Support\ExtensionManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    use FacilitatesImporting;

    public function testimonials()
    {
        $testimonials = Cache::remember('testimonials', now()->addHours(6), function () {
            return Testimonial::with('account')->get()->shuffle();
        });

        return Inertia::render('Public/Testimonials', compact('testimonials'));
    }

    public function features(ExtensionManager $extensionManager): Response
    {
        $extensions = $extensionManager->getPublic();

        return Inertia::render('Public/Features', compact('extensions'));
    }

    public function new(): Response
    {
        $posts = RecentNews::collect();

        return Inertia::render('Public/WhatsNew', compact('posts'));
    }

    public function importing()
    {
        $translatableFields = collect([
            'exams.sex',
            'exams.weight_unit',
            'exams.bcs',
            'exams.attitude',
            'exams.dehydration',
            'exams.mm_color',
            'exams.mm_texture',
            'patients.disposition',
            'patients.transfer_type',
            'patients.release_type',
            'patients.carcass_saved',
        ])->transform(function ($field) {
            return [
                'name' => __($field),
                'options' => fields()->getField($field, 'options'),
            ];
        });

        ExtensionManager::bootstrapPublic();
        session()->put('import.whatImporting', 'patients');

        $importableEntities = $this->importableEntities()->transform(function ($entity) {
            return [
                'name' => $entity,
                'slug' => Str::slug($entity),
            ];
        });

        $filteredRequiredFields = collect($this->filteredRequiredFields())->transform(function ($field) {
            return __($field);
        });

        return Inertia::render('Public/Importing', compact(
            'importableEntities',
            'filteredRequiredFields',
            'translatableFields'
        ));
    }
}
