<?php


namespace Dymantic\InstagramFeed;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AccessTokenController
{
    public function handleRedirect(Request $request)
    {
        $state = $request->input('state', '');
        $identity_token = str_split(Config::get('state_separator') ?? ',', $state)[0];
        $profile = Profile::usingIdentityToken($identity_token);
        
        if (!$profile) {
            Log::error('unable to retrieve IG profile');
            return Redirect::to(Config::get('instagram-feed.failure_redirect_to'));
        }

        try {
            $profile->requestToken($request);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return Redirect::to(Config::get('instagram-feed.failure_redirect_to'));
        }

        return Redirect::to(Config::get('instagram-feed.success_redirect_to'));
    }
}
