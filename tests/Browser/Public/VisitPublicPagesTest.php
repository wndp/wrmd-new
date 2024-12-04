<?php

namespace Tests\Browser\Public;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Tests\Traits\MailPit;

final class VisitPublicPagesTest extends DuskTestCase
{
    use MailPit;

    #[Test]
    public function visitWelcomePage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('home'))
                ->assertTitleContains('Welcome')
                ->assertSee('Wildlife Rehabilitation MD');
        });
    }

    #[Test]
    public function visitTestimonialsPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.testimonials'))
                ->assertTitleContains('Testimonials')
                ->assertSee('Testimonials');
        });
    }

    #[Test]
    public function visitFeaturesPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.features'))
                ->assertTitleContains('Features')
                ->assertSee('Out-of-the-box simple;');
        });
    }

    #[Test]
    public function visitPricePage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.pricing'))
                ->assertTitleContains('Pricing')
                ->assertSee('Pricing');
        });
    }

    #[Test]
    public function visitDonatePage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('donate.index'))
                ->assertTitleContains('Help Support Wildlife Rehabilitation MD')
                ->assertSee('Help Support Wildlife Rehabilitation MD');
        });
    }

    #[Test]
    public function visitThankYouPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('donate.thanks'))
                ->assertTitleContains('Thank You')
                ->assertSee('Thank You');
        });
    }

    #[Test]
    public function visitAboutPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about'))
                ->assertTitleContains('About Wildlife Rehabilitation MD')
                ->assertSee('About Wildlife Rehabilitation MD');
        });
    }

    #[Test]
    public function visitWhatsNewPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.new'))
                ->assertTitleContains("What's New")
                ->assertSee("What's New");
        });
    }

    #[Test]
    public function visitSecurityPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.security'))
                ->assertTitleContains('Security')
                ->assertSee('Security');
        });
    }

    #[Test]
    public function visitDataPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.data-integrity'))
                ->assertTitleContains('Data Integrity')
                ->assertSee('Data Integrity');
        });
    }

    #[Test]
    public function visitAgenciesPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.agencies'))
                ->assertTitleContains('Regulatory Agencies')
                ->assertSee('Regulatory Agencies');
        });
    }

    #[Test]
    public function visitSupportUsPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.support-us'))
                ->assertTitleContains('Support Us')
                ->assertSee('Support Us');
        });
    }

    #[Test]
    public function visitApiPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('api/v3')
                ->assertTitleContains('WRMD API Documentation')
                ->assertSee('Wildlife Rehabilitation MD (WRMD) API');
        });
    }

    #[Test]
    public function visitTermsAndConditionsPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.terms'))
                ->assertTitleContains('Terms And Conditions')
                ->assertSee('Terms And Conditions');
        });
    }

    #[Test]
    public function visitPrivacyPolicyPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.privacy'))
                ->assertTitleContains('Privacy Policy')
                ->assertSee('Privacy Policy');
        });
    }

    #[Test]
    public function visitInactiveAccountPolicyPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.inactive-account'))
                ->assertTitleContains('Inactive Account Policy')
                ->assertSee('Inactive Account Policy');
        });
    }

    #[Test]
    public function visitCookiesPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.cookies'))
                ->assertTitleContains('Cookies Policy')
                ->assertSee('Cookies Policy');
        });
    }

    #[Test]
    public function visitSlaPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.sla'))
                ->assertTitleContains('SLA')
                ->assertSee('SLA');
        });
    }

    #[Test]
    public function visitImportingPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('about.importing'))
                ->assertTitleContains('Importing')
                ->assertSee('Importing Patients');
        });
    }

    #[Test]
    public function visitContactPage(): void
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
