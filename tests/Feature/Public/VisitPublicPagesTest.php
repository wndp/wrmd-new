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
        $this->get('/')->assertOk()->assertInertia(function ($page) {
            $page->component('Welcome');
        });
    }

    #[Test]
    public function visitTestimonials(): void
    {
        $this->get('/testimonials')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Testimonials');
        });
    }

    #[Test]
    public function visitFeatures(): void
    {
        $this->get('/features')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Features')
                ->has('extensions');
        });
    }

    #[Test]
    public function visitPrice(): void
    {
        $this->get('pricing')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Pricing');
        });
    }

    #[Test]
    public function visitDonate(): void
    {
        $this->get('donate')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Donate');
        });
    }

    #[Test]
    public function visitThankYou(): void
    {
        $this->get('thank-you')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Thanks');
        });
    }

    #[Test]
    public function visitAbout(): void
    {
        $this->get('about')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/About');
        });
    }

    #[Test]
    public function visitWhatsNew(): void
    {
        $this->get('whats-new')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/WhatsNew');
        });
    }

    #[Test]
    public function visitSecurity(): void
    {
        $this->get('security')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Security');
        });
    }

    #[Test]
    public function visitData(): void
    {
        $this->get('data-integrity')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/DataIntegrity');
        });
    }

    #[Test]
    public function visitAgencies(): void
    {
        $this->get('agencies')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Agencies');
        });
    }

    #[Test]
    public function visitSupportUs(): void
    {
        $this->get('support-us')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/SupportUs');
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
        $this->get('terms-and-conditions')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Terms');
        });
    }

    #[Test]
    public function visitPrivacy(): void
    {
        $this->get('privacy-policy')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Privacy');
        });
    }

    #[Test]
    public function visitCookies(): void
    {
        $this->get('cookies-policy')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Cookies');
        });
    }

    #[Test]
    public function visitSla(): void
    {
        $this->get('sla')->assertOk()->assertInertia(function ($page) {
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

        $this->get('importing')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Importing');
        });
    }

    #[Test]
    public function visitContact(): void
    {
        $this->get('/contact')->assertOk()->assertInertia(function ($page) {
            $page->component('Public/Contact');
        });
    }
}
