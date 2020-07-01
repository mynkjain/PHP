<?php

namespace Microsoft\BingAds\Samples;
ini_set ( 'max_execution_time', 1200); 
require_once "./vendor/autoload.php";

include "WebAuthHelper.php";
include __DIR__ . "/V13/CampaignManagementExampleHelper.php";

// Specify the Microsoft\BingAds\Auth classes that will be used.
use Microsoft\BingAds\Auth\AuthorizationData;
use Microsoft\BingAds\Auth\ServiceClient;
use Microsoft\BingAds\Auth\ServiceClientType;

// Specify the Microsoft\BingAds\V13\CustomerManagement classes that will be used.
use Microsoft\BingAds\V13\CustomerManagement\GetUserRequest;
use Microsoft\BingAds\V13\CustomerManagement\SearchAccountsRequest;
use Microsoft\BingAds\V13\CustomerManagement\Paging;
use Microsoft\BingAds\V13\CustomerManagement\Predicate;
use Microsoft\BingAds\V13\CustomerManagement\PredicateOperator;

// Specify the Microsoft\BingAds\Samples classes that will be used.
use Microsoft\BingAds\Samples\WebAuthHelper;

// Specify the Microsoft\BingAds\V13\CampaignManagement classes that will be used.
use Microsoft\BingAds\V13\CampaignManagement\Campaign;
use Microsoft\BingAds\V13\CampaignManagement\BudgetLimitType;

// Specify the Microsoft\BingAds\Samples classes that will be used.
use Microsoft\BingAds\Samples\V13\CampaignManagementExampleHelper;

use Exception;

session_start();
// Disable WSDL caching.
ini_set("soap.wsdl_cache_enabled", "0");
ini_set("soap.wsdl_cache_ttl", "0");
// If there is no user authenticated, go back to the site index.

if(!isset($_SESSION['AuthorizationData']) || 
!isset($_SESSION['AuthorizationData']->Authentication) || 
!isset($_SESSION['AuthorizationData']->Authentication->OAuthTokens)
)
{
    header('Location: '. 'http://localhost/bingAds/');
}
else {
    
    // If a refresh token is already present, use it to request new access and refresh tokens.
    // You should store refresh tokens securely i.e. not in session as shown in this demo.

    $refreshToken = $_SESSION['AuthorizationData']->Authentication->OAuthTokens->RefreshToken;
    if($refreshToken != null)
    {
        $_SESSION['AuthorizationData']->Authentication->RequestOAuthTokensByRefreshToken($refreshToken);
    }
    $GLOBALS['AuthorizationData'] = $_SESSION['AuthorizationData'];
    printf("Access token: %s<br/>", $GLOBALS['AuthorizationData']->Authentication->OAuthTokens->AccessToken);
    printf("Refresh token: %s<br/>", $GLOBALS['AuthorizationData']->Authentication->OAuthTokens->RefreshToken);

    $GLOBALS['CustomerManagementProxy'] = new ServiceClient(
        ServiceClientType::CustomerManagementVersion13, 
        $GLOBALS['AuthorizationData'], 
        WebAuthHelper::GetApiEnvironment());

    // Set the GetUser request parameter to an empty user identifier to get the current 
    // authenticated Microsoft Advertising user, and then search for all accounts the user can access.

    $getUserRequest = new GetUserRequest();
    $getUserRequest->UserId = null;

    $user = $GLOBALS['CustomerManagementProxy']->GetService()->GetUser($getUserRequest)->User;

    // Search for the Microsoft Advertising accounts that the user can access.

    $pageInfo = new Paging();
    $pageInfo->Index = 0;    // The first page
    $pageInfo->Size = 1000;   // The first 1,000 accounts for this page of results    
    $predicate = new Predicate();
    $predicate->Field = "UserId";
    $predicate->Operator = PredicateOperator::Equals;
    $predicate->Value = $user->Id; 

    $searchAccountsRequest = new SearchAccountsRequest();
    $searchAccountsRequest->Predicates = array($predicate);
    $searchAccountsRequest->Ordering = null;
    $searchAccountsRequest->PageInfo = $pageInfo;

    $accounts = $GLOBALS['CustomerManagementProxy']->GetService()->SearchAccounts($searchAccountsRequest)->Accounts;

    print "-----<br/>Accounts the user can access:<br/>";
    foreach ($accounts->AdvertiserAccount as $account)
    {
        printf("Account Name: %s<br/>", $account->Name);
    }

    // We'll use the first account by default for the examples. 
    echo "<br>";
    echo "<br>";
    echo "<pre>";
    echo $GLOBALS['AuthorizationData']->AccountId = $accounts->AdvertiserAccount[0]->Id;
    echo "<br>";
    echo $GLOBALS['AuthorizationData']->CustomerId = $accounts->AdvertiserAccount[0]->ParentCustomerId;

    $GLOBALS['AdInsightProxy'] = new ServiceClient(
        ServiceClientType::AdInsightVersion13, 
        $GLOBALS['AuthorizationData'], 
        WebAuthHelper::GetApiEnvironment()
    );
     echo "string";
    $GLOBALS['BulkProxy'] = new ServiceClient(
        ServiceClientType::BulkVersion13, 
        $GLOBALS['AuthorizationData'], 
        WebAuthHelper::GetApiEnvironment()
    );

    $GLOBALS['CampaignManagementProxy'] = new ServiceClient(
        ServiceClientType::CampaignManagementVersion13, 
        $GLOBALS['AuthorizationData'], 
        WebAuthHelper::GetApiEnvironment()
    );

    $GLOBALS['CustomerManagementProxy'] = new ServiceClient(
        ServiceClientType::CustomerManagementVersion13, 
        $GLOBALS['AuthorizationData'], 
        WebAuthHelper::GetApiEnvironment()
    );

    $GLOBALS['ReportingProxy'] = new ServiceClient(
        ServiceClientType::ReportingVersion13, 
        $GLOBALS['AuthorizationData'], 
        WebAuthHelper::GetApiEnvironment()
    );
    // Add a campaign to associate with ad extensions.
    // $campaigns = array();   
    // $campaign = new Campaign();
    // print_r($campaign);
    // $campaign->Name = "Erlic's Campaign";
    // $campaign->BudgetType = BudgetLimitType::DailyBudgetStandard;
    // $campaign->DailyBudget = 1.00;
    // $campaign->Languages = array("English");
    // $campaign->TimeZone = "PacificTimeUSCanadaTijuana";
    // $campaigns[] = $campaign;
    
    // print("-----\r\nAddCampaigns:\r\n");
    // try{
    //     $addCampaignsResponse = CampaignManagementExampleHelper::AddCampaigns(
    //         $GLOBALS['AuthorizationData']->AccountId, 
    //         $campaigns
    //     );
    //     $campaignIds = $addCampaignsResponse->CampaignIds;
    //     print("CampaignIds:\r\n");
    //     CampaignManagementExampleHelper::OutputArrayOfLong($campaignIds);
    //     print("PartialErrors:\r\n");
    //     CampaignManagementExampleHelper::OutputArrayOfBatchError($addCampaignsResponse->PartialErrors);
    // }catch(Exception $e){
    //     echo $e->getMessage();
    // }

    // Get all campaigns BY account ID
    // $getCampaigns = CampaignManagementExampleHelper::GetCampaignsByAccountId(
    //         $GLOBALS['AuthorizationData']->AccountId, 
    //         '');
    // print_r($getCampaigns);
    
    // Create Adgroup by CampID 919717330, Adgroup ID
    // $getCampaigns = CampaignManagementExampleHelper::AddAds(
    //         $adGroupId, 
    //         $ads);

    //Get Ads Group By campaign ID 919717330
    $getAdGroups = CampaignManagementExampleHelper::GetAdGroupsByCampaignId(
            919717330);
    print_r($getAdGroups);

}

//session_unset();

?>