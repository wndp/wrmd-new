<?php

namespace Tests\Feature\Public;

use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class VisitPublicPagesTest extends TestCase
{
    #[Test]
    public function visitHome(): void
    {
        $this->get(route('home'))->assertOk()->assertInertia(function ($page) {
            $page->component('Welcome');
        });
    }

    #[Test]
    public function visitTestimonials(): void
    {
        $this->get(route('about.testimonials'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Testimonials');
        });
    }

    #[Test]
    public function visitFeatures(): void
    {
        $this->get(route('about.features'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Features')
                ->hasAll(['standardExtensions', 'proExtensions']);
        });
    }

    #[Test]
    public function visitPrice(): void
    {
        $this->get(route('about.pricing'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Pricing');
        });
    }

    #[Test]
    public function visitDonate(): void
    {
        $this->get(route('donate.index'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Donate');
        });
    }

    #[Test]
    public function visitThankYou(): void
    {
        $this->get(route('donate.thanks'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Thanks');
        });
    }

    #[Test]
    public function visitAbout(): void
    {
        $this->get(route('about'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/About');
        });
    }

    #[Test]
    public function visitWhatsNew(): void
    {
        $this->get(route('about.new'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/WhatsNew');
        });
    }

    #[Test]
    public function visitSecurityAndDataIntegrity(): void
    {
        $this->get(route('about.security'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Security');
        });
    }

    #[Test]
    public function visitAgencies(): void
    {
        $this->get(route('about.agencies'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Agencies');
        });
    }

    #[Test]
    public function visitOilSpills(): void
    {
        $this->get(route('about.oil-spills'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/OilSpills');
        });
    }

    #[Test]
    public function visitWildAlert(): void
    {
        $this->get(route('about.wildalert'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/WildAlert');
        });
    }

    #[Test]
    public function visitApi(): void
    {
        $this->get('api/v3')->assertOk();
    }

    #[Test]
    public function visitTerms(): void
    {
        $this->get(route('about.terms'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Terms');
        });
    }

    #[Test]
    public function visitPrivacy(): void
    {
        $this->get(route('about.privacy'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Privacy');
        });
    }

    #[Test]
    public function visitInactiveAccountPolicy(): void
    {
        $this->get(route('about.inactive-account'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/InactiveAccount');
        });
    }

    #[Test]
    public function visitSla(): void
    {
        $this->get(route('about.sla'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Sla');
        });
    }

    #[Test]
    public function visitImporting(): void
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

    #[Test]
    public function visitContact(): void
    {
        $this->get(route('contact.create'))->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Contact');
        });
    }
}
