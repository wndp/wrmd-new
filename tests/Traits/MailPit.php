<?php

namespace Tests\Traits;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\After;

trait MailPit
{
    /**
     * @var GuzzleHttp\Client
     */
    private $mailpit = 'http://host.docker.internal:8025/api/v1';

    /**
     * Return the provided email response or the last email sent.
     *
     * @param  mixed  $email
     */
    public function resolveEmail($email)
    {
        return is_null($email) ? $this->getLastEmail() : $email;
    }

    /**
     * Delete all emails.
     */
    #[After]
    public function deleteAllEmails()
    {
        Http::delete("{$this->mailpit}/messages");
    }

    /**
     * Get all emails.
     *
     * @return string | json
     */
    public function getAllEmails()
    {
        $emails = Http::get("{$this->mailpit}/messages")->json();

        if (empty($emails)) {
            $this->fail('No messages returned.');
        }

        return $emails;
    }

    /**
     * Get an email by its array key position.
     */
    public function getEmailByKey(string $key)
    {
        $emailId = array_key_exists($key, $this->getAllEmails()['messages']) ? $this->getAllEmails()['messages'][$key]['ID'] : null;

        try {
            return Http::get("{$this->mailpit}/message/{$emailId}")->json();
        } catch (ClientException $e) {
            return null;
        }
    }

    /**
     * Get an email's source by its id.
     */
    public function getEmailSourceById(string $emailId)
    {
        try {
            return $this->mailpit->get("/api/v1/messages/{$emailId}.source")->getBody()->getContents();
        } catch (ClientException $e) {
            return null;

        }
    }

    /**
     * Get last email.
     */
    public function getLastEmail()
    {
        return $this->getEmailByKey(0);
    }

    /**
     * Get regular expression matches from an email.
     */
    public function grabMatchesFromEmail(string $regex, $email = null)
    {
        $source = $this->resolveEmail($email)['Content']['Body'];

        $source = quoted_printable_decode($source); //str_replace(["=\r\n", "\r\n", "\r", "\n"], '', $source);

        preg_match($regex, $source, $matches);

        $this->assertNotEmpty($matches, "No matches found for $regex");

        return $matches;
    }

    /**
     * Assert email subject contains a string.
     */
    public function assertEmailSubjectContains(string $expected, $email = null): self
    {
        $actual = $this->resolveEmail($email)['Subject'];

        $this->assertStringContainsString(
            $expected,
            $actual,
            "Email subject [$actual] does not match [$expected]"
        );

        return $this;
    }

    /**
     * Assert email subject does not contain a string.
     */
    public function assertNotEmailSubjectContains(string $expected, $email = null): self
    {
        $this->assertNotContains($expected, $this->resolveEmail($email)['Content']['Headers']['Subject'][0]);

        return $this;
    }

    /**
     * Assert email body contains a string.
     */
    public function assertEmailBodyContains(string $expected, $email = null): self
    {
        $this->assertStringContainsString($expected, $this->resolveEmail($email)['Text']);

        return $this;
    }

    /**
     * Assert email body does not contain a string.
     */
    public function assertNotEmailBodyContains(string $expected, $email = null): self
    {
        $this->assertNotContains($expected, $this->resolveEmail($email)['Content']['Body']);

        return $this;
    }

    /**
     * Assert email was sent to a recipient.
     */
    public function assertEmailWasSentTo(string $expectedRecipient, $email = null): self
    {
        $this->assertStringContainsString($expectedRecipient, $this->resolveEmail($email)['To'][0]['Address']);

        return $this;
    }

    /**
     * Assert email was not sent to a recipient.
     */
    public function assertNotEmailWasSentTo(string $expectedRecipient, $email = null): self
    {
        $this->assertNotContains($expectedRecipient, $this->resolveEmail($email)['Content']['Headers']['To'][0]);

        return $this;
    }

    /**
     * Assert email reply to address contains a given string.
     */
    public function assertEmailReplyTo(string $replyTo, ?Response $email = null): self
    {
        $this->assertStringContainsString($replyTo, $this->resolveEmail($email)['Content']['Headers']['Reply-To'][0]);

        return $this;
    }

    /**
     * Assert email reply to address does not contain a given string.
     */
    public function assertNotEmailReplyTo(string $replyTo, ?Response $email = null): self
    {
        $this->assertNotContains($replyTo, $this->resolveEmail($email)['Content']['Headers']['Reply-To'][0]);

        return $this;
    }

    /**
     * Assert email has an attachment by file name.
     */
    public function assertEmailHasAttachment(string $attachment, $email = null): self
    {
        $this->assertStringContainsString($attachment, $this->resolveEmail($email)['Content']['Body']);

        return $this;
    }

    /**
     * Assert email does not have an attachment by file name.
     */
    public function assertNotEmailHasAttachment(string $attachment, $email = null): self
    {
        $this->assertNotContains($attachment, $this->resolveEmail($email)['Content']['Body']);

        return $this;
    }
}
