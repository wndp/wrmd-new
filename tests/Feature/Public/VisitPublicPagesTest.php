<?php

namespace Tests\Feature\Public;

use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use Tests\TestCase;

final class VisitPublicPagesTest extends TestCase
{
    public function test_visit_home(): void
    {
        $this->get(route('home'))->assertOk()->assertInertia(function ($page) {
            $page->component('Welcome');
        });
    }

    public function test_visit_testimonials(): void
    {
        $this->get(route('about.testimonials'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Testimonials');
        });
    }

    public function test_visit_features(): void
    {
        $this->get(route('about.features'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Features')
                ->hasAll(['standardExtensions', 'proExtensions']);
        });
    }

    public function test_visit_price(): void
    {
        $this->get(route('about.pricing'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Pricing');
        });
    }

    public function test_visit_donate(): void
    {
        $this->get(route('donate.index'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Donate');
        });
    }

    public function test_visit_thank_you(): void
    {
        $this->get(route('donate.thanks'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Thanks');
        });
    }

    public function test_visit_about(): void
    {
        $this->get(route('about'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/About');
        });
    }

    public function test_visit_whats_new(): void
    {
        $this->get(route('about.new'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/WhatsNew');
        });
    }

    public function test_visit_security_and_data_integrity(): void
    {
        $this->get(route('about.security'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Security');
        });
    }

    public function test_visit_agencies(): void
    {
        $this->get(route('about.agencies'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Agencies');
        });
    }

    public function test_visit_oil_spills(): void
    {
        $this->get(route('about.oil-spills'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/OilSpills');
        });
    }

    public function test_visit_wild_alert(): void
    {
        $this->get(route('about.wildalert'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/WildAlert');
        });
    }

    public function test_visit_api(): void
    {
        $this->get('api/v3')->assertOk();
    }

    public function test_visit_terms(): void
    {
        $this->get(route('about.terms'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Terms');
        });
    }

    public function test_visit_privacy(): void
    {
        $this->get(route('about.privacy'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Privacy');
        });
    }

    public function test_visit_inactive_account_policy(): void
    {
        $this->get(route('about.inactive-account'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/InactiveAccount');
        });
    }

    public function test_visit_sla(): void
    {
        $this->get(route('about.sla'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Sla');
        });
    }

    public function test_visit_importing(): void
    {
        AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_DISPOSITIONS]);
        AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_SEXES]);
        AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_WEIGHT_UNITS]);
        AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_BODY_CONDITIONS]);
        AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_ATTITUDES]);
        AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_DEHYDRATIONS]);
        AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_MUCUS_MEMBRANE_COLORS]);
        AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_MUCUS_MEMBRANE_TEXTURES]);

        $this->get(route('about.importing'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Importing');
        });
    }

    public function test_visit_contact(): void
    {
        $this->get(route('contact.create'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Contact');
        });
    }
}
