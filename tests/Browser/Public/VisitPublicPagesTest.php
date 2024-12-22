<?php

namespace Tests\Browser\Public;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Traits\MailPit;

final class VisitPublicPagesTest extends DuskTestCase
{
    use MailPit;

    public function test_visit_welcome_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('home'))
                ->assertTitleContains('Welcome')
                ->assertSee('Wildlife Rehabilitation MD');
        });
    }

    public function test_visit_testimonials_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.testimonials'))
                ->assertTitleContains('Testimonials')
                ->assertSee('Testimonials');
        });
    }

    public function test_visit_features_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.features'))
                ->assertTitleContains('Features')
                ->assertSee('Out-of-the-box simple;');
        });
    }

    public function test_visit_price_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.pricing'))
                ->assertTitleContains('Pricing')
                ->assertSee('Pricing');
        });
    }

    public function test_visit_donate_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('donate.index'))
                ->assertTitleContains('Help Support Wildlife Rehabilitation MD')
                ->assertSee('Help Support Wildlife Rehabilitation MD');
        });
    }

    public function test_visit_thank_you_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('donate.thanks'))
                ->assertTitleContains('Thank You')
                ->assertSee('Thank You');
        });
    }

    public function test_visit_about_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about'))
                ->assertTitleContains('About Wildlife Rehabilitation MD')
                ->assertSee('About Wildlife Rehabilitation MD');
        });
    }

    public function test_visit_whats_new_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.new'))
                ->assertTitleContains("What's New")
                ->assertSee("What's New");
        });
    }

    public function test_visit_security_and_data_integrity_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.security'))
                ->assertTitleContains('Security')
                ->assertSee('Security and Data Integrity');
        });
    }

    public function test_visit_agencies_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.agencies'))
                ->assertTitleContains('Regulatory Agencies')
                ->assertSee('Regulatory Agencies');
        });
    }

    public function test_visit_oil_spill_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.oil-spills'))
                ->assertTitleContains('Oil Spills')
                ->assertSee('Oil Spills');
        });
    }

    public function test_visit_wild_alert_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.wildalert'))
                ->assertTitleContains('WildAlert')
                ->assertSee('WildAlert');
        });
    }

    public function test_visit_api_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('api/v3')
                ->assertTitleContains('WRMD API Documentation')
                ->assertSee('Wildlife Rehabilitation MD (WRMD) API');
        });
    }

    public function test_visit_terms_and_conditions_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.terms'))
                ->assertTitleContains('Terms And Conditions')
                ->assertSee('Terms And Conditions');
        });
    }

    public function test_visit_privacy_policy_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.privacy'))
                ->assertTitleContains('Privacy Policy')
                ->assertSee('Privacy Policy');
        });
    }

    public function test_visit_inactive_account_policy_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.inactive-account'))
                ->assertTitleContains('Inactive Account Policy')
                ->assertSee('Inactive Account Policy');
        });
    }

    public function test_visit_sla_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.sla'))
                ->assertTitleContains('SLA')
                ->assertSee('SLA');
        });
    }

    public function test_visit_importing_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.importing'))
                ->assertTitleContains('Importing')
                ->assertSee('Importing Patients');
        });
    }

    public function test_visit_contact_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('contact.create'))
                ->assertTitleContains('Contact Us')
                ->assertSee('Contact Us')
                ->type('name', 'Jim Halpert')
                ->type('organization', 'Dunder Mifflin')
                ->type('email', 'jim@dundermifflin.com')
                ->type('subject', 'Using WRMD')
                ->type('message', 'This is great!')
                ->press('SEND MESSAGE')
                ->waitForText('Thanks for reaching out to us')
                ->assertInputValue('name', '')
                ->assertInputValue('email', '')
                ->assertInputValue('organization', '')
                ->assertInputValue('subject', '')
                ->assertInputValue('message', '');
        });

        $this->assertEmailWasSentTo('support@wildneighborsdp.org')
            ->assertEmailSubjectContains('Using WRMD')
            ->assertEmailBodyContains('This is great!');
    }
}
