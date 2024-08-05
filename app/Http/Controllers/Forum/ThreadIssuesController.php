<?php

namespace App\Http\Controllers\Forum;

use App\Domain\Accounts\SpecialtyAccounts;
use App\Domain\Forum\Thread;
use App\Http\Controllers\Controller;
use Exception;
use Github\Client;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ThreadIssuesController extends Controller
{
    /**
     * Search for an open GitHub issue for the provided thread.
     */
    public function show(Thread $thread): JsonResponse
    {
        //abort_unless(Auth::user()->current_account_id === SpecialtyAccounts::WRMD, 404);

        $result = $this->getGitHubIssue($thread);

        if ($result['total_count'] === 1) {
            return response()->json([
                'exists' => true,
                'data' => $result['items'][0],
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Store a thread in GitHub issues.
     */
    public function store(Thread $thread): JsonResponse
    {
        abort_unless(Auth::user()->current_account_id === SpecialtyAccounts::WRMD, 404);

        try {
            $this->setAuthAccessToken();

            GitHub::issues()->create('wndp', 'wrmd', [
                'title' => $thread->issue_title,
                'body' => $thread->issue_body,
                'labels' => $thread->issue_labels,
            ]);

            return response()->json(['posted' => true]);
        } catch (Exception $e) {
            return response()->json(['posted' => false]);
        }
    }

    /**
     * Mark a thread in GitHub issues as closed.
     */
    public function destroy(Thread $thread): JsonResponse
    {
        abort_unless(Auth::user()->current_account_id === SpecialtyAccounts::WRMD, 404);

        $result = $this->getGitHubIssue($thread);

        if ($result['total_count'] === 1) {
            try {
                GitHub::issues()->update('wndp', 'wrmd', $result['items'][0]['number'], [
                    'state' => 'closed',
                ]);

                return response()->json(['closed' => true]);
            } catch (Exception $e) {
                return response()->json(['closed' => false]);
            }
        }

        return response()->json(['closed' => false]);
    }

    /**
     * Search for an open GitHub issue for the provided thread.
     */
    protected function getGitHubIssue(Thread $thread): array
    {
        $this->setAuthAccessToken();

        return GitHub::search()->issues("is:issue is:open repo:wndp/wrmd \"WRMD Forum: {$thread->id}\"");
    }

    /**
     * Set the GitHub authorization token.
     */
    private function setAuthAccessToken()
    {
        $token = GitHub::apps()->createInstallationToken(314508);
        GitHub::authenticate($token['token'], Client::AUTH_ACCESS_TOKEN);
    }
}
