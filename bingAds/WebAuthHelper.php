<?php

namespace Microsoft\BingAds\Samples;

require_once "./vendor/autoload.php";

// Specify the Microsoft\BingAds\Auth classes that will be used.
use Microsoft\BingAds\Auth\ApiEnvironment;

/** 
* Defines global settings that you can use for testing your application.
* Your production implementation may vary, and you should always store sensitive information securely.
*/
final class WebAuthHelper {

    const DeveloperToken = 'BBD37VB98';
    const ApiEnvironment = ApiEnvironment::Sandbox;
    const ClientId = '443a60f5-cdab-463d-ab21-02ec5da1a2a3'; 
    const ClientSecret = 'XW9DFK~_r4-i3f0kXVR1k27-cV~wv-p5yB'; 
    const RedirectUri = 'http://localhost/bingAds/OAuth2Callback.php'; 

    static function GetApiEnvironment() 
    {
        return WebAuthHelper::ApiEnvironment;
    }
}