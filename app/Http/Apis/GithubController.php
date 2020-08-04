<?php

namespace App\Http\Apis;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Request;

class GithubController
{
    public function webhook(Request $request)
    {
        $headers = $request->headers;
        $event = $headers->get('x-github-event');

        switch ($event) {
            case 'pull_request':
                $payload = json_decode($request->getContent());
                if (isset($payload->action) && $payload->action == 'closed' && $payload->pull_request->merged == 1) {
                    Artisan::call('system:update');
                }
                break;
        }
    }
}
