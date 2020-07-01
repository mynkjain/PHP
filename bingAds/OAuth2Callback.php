<?php
ini_set('max_execution_time', 1200);

require_once "./vendor/autoload.php";
require_once "./Developer/BingAdsHelper.php";

// Specify the Microsoft\BingAds\V13\CustomerManagement classes that will be used.
use Microsoft\BingAds\V13\CustomerManagement\GetUserRequest;
use Microsoft\BingAds\V13\CustomerManagement\SearchAccountsRequest;
use Microsoft\BingAds\V13\CustomerManagement\Paging;
use Microsoft\BingAds\V13\CustomerManagement\Predicate;
use Microsoft\BingAds\V13\CustomerManagement\PredicateOperator;
use Microsoft\BingAds\V13\CampaignManagement\GetAdGroupsByCampaignIdRequest;

// Specify the Microsoft\BingAds\V13\CampaignManagement classes that will be used.
use Microsoft\BingAds\V13\CampaignManagement\Campaign;
use Microsoft\BingAds\V13\CampaignManagement\BudgetLimitType;
use Microsoft\BingAds\V13\CampaignManagement\AdGroup;
use Microsoft\BingAds\V13\CampaignManagement\Date;
use Microsoft\BingAds\V13\CampaignManagement\Bid;
use Microsoft\BingAds\V13\CampaignManagement\ExpandedTextAd;
use Microsoft\BingAds\V13\CampaignManagement\Keyword;
use Microsoft\BingAds\V13\CampaignManagement\MatchType;

//Developer classes
use Developer\BingAds\BingAdsHelper;



// Start the session
session_start();

if(isset($_SESSION["auth_data"])){
	echo "<pre>";
    // Get new Access & refresh token by Old refresh token
	BingAdsHelper::getNewAccessAndRefrehToken(); // Auth Data updated in $_SESSION
	$accounts = BingAdsHelper::getAccounts();
    //print "-----<br/>Accounts the user can access:<br/>";
    $total_accounts = count($accounts->AdvertiserAccount);
    // foreach ($accounts->AdvertiserAccount as $account)
    // {
    //     printf("Account Name: %s<br/>", $account->Name);
    // }
    if($total_accounts > 1){
    	// Fetching details of first account
        $AccountId = $accounts->AdvertiserAccount[0]->Id;
	    
	    $CustomerId = $accounts->AdvertiserAccount[0]->ParentCustomerId; 
    }else{
    	$AccountId = $accounts->AdvertiserAccount->Id;
	    
	    $CustomerId = $accounts->AdvertiserAccount->ParentCustomerId; 

    }

    // Add a campaign .
    $campaigns = array();   
    $campaign = new Campaign();
    $campaign->Name = "Richard's Campaign 40";
    $campaign->BudgetType = BudgetLimitType::DailyBudgetStandard;
    $campaign->DailyBudget = 1.00;
    $campaign->Languages = array("English", "Finnish");
    $campaign->TimeZone = "Santiago";
    $campaigns[] = $campaign;
    
    print("-----\r\nAddCampaigns:\r\n");
    try{
        $response = BingAdsHelper::AddCampaigns(
            $AccountId, $CustomerId,
            $campaigns
        );
        // $campaignIds = $addCampaignsResponse->CampaignIds;
        // $campaignId = $addCampaignsResponse->CampaignIds->long;
        // print("CampaignIds:\r\n");
        // CampaignManagementExampleHelper::OutputArrayOfLong($campaignIds);
        // print("PartialErrors:\r\n");
        // CampaignManagementExampleHelper::OutputArrayOfBatchError($addCampaignsResponse->PartialErrors);
    }catch (SoapFault $fault){
            print_r($fault->getMessage());
            die;
    }
    echo $CampaignIds =  $response->CampaignIds->long;
    echo "<br>"; 
    // Get Adgroups by Campaign ID
		// try{
	 //      $response = BingAdsHelper::GetAdGroupsByCampaignId($AccountId, $CustomerId, 919717330);
		// }catch (SoapFault $fault){
		// 	print_r($fault);
	 //         die;
		// }

    // Create the ad group that will have the product partitions.

    $adGroups = array();
    $adGroup = new AdGroup();
    $adGroup->CpcBid = new Bid();
    $adGroup->CpcBid->Amount = 0.5;
    date_default_timezone_set('UTC');
    $endDate = new Date();
    $endDate->Day = 31;
    $endDate->Month = 12;
    $endDate->Year = date("Y");
    $adGroup->EndDate = $endDate;
    $adGroup->Name = "Football Sale 2017";    
    $adGroup->StartDate = null;
    $adGroup->Status = 'Active';    
    $adGroups[] = $adGroup;
    
    $addAdGroupsResponse = BingAdsHelper::AddAdGroups($AccountId, 
    	$CustomerId,
        $CampaignIds, 
        $adGroups,
        null
    );
    $adGroupIds = $addAdGroupsResponse->AdGroupIds;
    print_r($addAdGroupsResponse);
    echo "<br>";
    print_r($adGroupIds);
    echo "<br>";
    // Add keywords and ads within the ad group.

    $keywords = array();
    $keyword = new Keyword();
    // $keyword->Bid = new Bid();
    // $keyword->Bid->Amount = 0.47;
    // $keyword->Param2 = "10% Off";
    $keyword->MatchType = MatchType::Broad;
    $keyword->Text = "Brand-A Shoes";
    $keywords[] = $keyword;

    print("-----\r\nAddKeywords:\r\n");
    $addKeywordsResponse = BingAdsHelper::AddKeywords($AccountId, 
        $CustomerId,
        $adGroupIds->long, 
        $keywords,
        null
    );
    
    // Create Ad in adGroup
    $ads = array();
    $expandedTextAd = new ExpandedTextAd();
    $expandedTextAd->TitlePart1 = "Contoso";
    $expandedTextAd->TitlePart2 = "Quick & Easy Setup";
    $expandedTextAd->TitlePart3 = "Seemless Integration";
    $expandedTextAd->Text = "Find New Customers & Increase Sales!";
    $expandedTextAd->TextPart2 = "Start Advertising on Contoso Today.";
    $expandedTextAd->Path1 = "seattle";
    $expandedTextAd->Path2 = "shoe sale";
    $expandedTextAd->FinalUrls = array("http://www.contoso.com/womenshoesale");
    $ads[] = new SoapVar(
        $expandedTextAd, 
        SOAP_ENC_OBJECT, 
        'ExpandedTextAd', 
        'https://bingads.microsoft.com/CampaignManagement/v13'
    );
	$addAdsResponse = BingAdsHelper::AddAds($AccountId, 
		$CustomerId,
        $adGroupIds->long, 
        $ads
    );
	echo "<br>";
	print_r($addAdsResponse);

    

}else{
	if(isset($_GET['code'])){
		echo $_GET['code'];
		$curl = curl_init();
		$data = array('client_id' => '443a60f5-cdab-463d-ab21-02ec5da1a2a3',
		            'scope' => 'bingads.manage',
		            'code' => $_GET['code'],
		            'grant_type' => 'authorization_code',
		            'redirect_uri' => 'http://localhost/bingAds/OAuth2Callback.php',
		            'client_secret' => 'XW9DFK~_r4-i3f0kXVR1k27-cV~wv-p5yB'
	            );

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://login.live-int.com/oauth20_token.srf",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => http_build_query($data),
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/x-www-form-urlencoded",
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		if(empty($err)){
            $_SESSION["auth_data"] = json_decode($response);
            $_SESSION["code"] = $_GET['code'];
		}else{
			echo $err;
		}
	}

}

?>