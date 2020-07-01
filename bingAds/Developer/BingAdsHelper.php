<?php
namespace Developer\BingAds;
// Disable WSDL caching.
ini_set("soap.wsdl_cache_enabled", "0");
ini_set("soap.wsdl_cache_ttl", "0");
require_once __DIR__ ."/../vendor/autoload.php";
// Specify the Microsoft\BingAds\V13\CustomerManagement classes that will be used.
use Microsoft\BingAds\V13\CustomerManagement\GetUserRequest;
use Microsoft\BingAds\V13\CustomerManagement\SearchAccountsRequest;
use Microsoft\BingAds\V13\CustomerManagement\Paging;
use Microsoft\BingAds\V13\CustomerManagement\Predicate;
use Microsoft\BingAds\V13\CustomerManagement\PredicateOperator;

// Specify the Microsoft\BingAds\V13\CampaignManagement classes that will be used.
use Microsoft\BingAds\V13\CampaignManagement\AddCampaignsRequest;
use Microsoft\BingAds\V13\CampaignManagement\GetAdGroupsByCampaignIdRequest;
use Microsoft\BingAds\V13\CampaignManagement\AddAdGroupsRequest;
use Microsoft\BingAds\V13\CampaignManagement\AddAdsRequest;
use Microsoft\BingAds\V13\CampaignManagement\AddKeywordsRequest;
use Microsoft\BingAds\V13\CampaignManagement\GetCampaignsByAccountIdRequest;
use Microsoft\BingAds\V13\CampaignManagement\UpdateCampaignsRequest;
use Microsoft\BingAds\V13\CampaignManagement\GetKeywordsByAdGroupIdRequest;
use Microsoft\BingAds\V13\CampaignManagement\DeleteKeywordsRequest;
use Microsoft\BingAds\V13\CampaignManagement\AddCampaignCriterionsRequest;
use Microsoft\BingAds\V13\CampaignManagement\GetGeoLocationsFileUrlRequest;
use Microsoft\BingAds\V13\CampaignManagement\GetCampaignCriterionsByIdsRequest;
use Microsoft\BingAds\V13\CampaignManagement\DeleteCampaignCriterionsRequest;
use Microsoft\BingAds\V13\CampaignManagement\UpdateAdsRequest;
use Microsoft\BingAds\V13\CampaignManagement\DeleteAdsRequest;
use Microsoft\BingAds\V13\CampaignManagement\DeleteCampaignsRequest;

use \SoapHeader as SoapHeader;
use \SoapClient as SoapClient;

/**
 * 
 */
class BingAdsHelper
{   
	// SandBox
	// const ClientID = '443a60f5-cdab-463d-ab21-02ec5da1a2a3';
	// const ClientSecret = "XW9DFK~_r4-i3f0kXVR1k27-cV~wv-p5yB";
	// const AuthURI = 'https://login.live-int.com/oauth20_token.srf';
	// const LoginURI = 'https://login.live-int.com/oauth20_authorize.srf';
	// const CampaignNamespace = 'https://bingads.microsoft.com/CampaignManagement/v13';
	// const DeveloperToken = 'BBD37VB98';
	// const CampaignManagementAPI = 'https://campaign.api.sandbox.bingads.microsoft.com/Api/Advertiser/CampaignManagement/v13/CampaignManagementService.svc?singleWsdl';
	// const CustomerServiceNamespace = 'https://bingads.microsoft.com/Customer/v13';
	// const CustomerManagementServiceAPI = 'https://clientcenter.api.sandbox.bingads.microsoft.com/Api/CustomerManagement/v13/CustomerManagementService.svc?singleWsdl';

	// Production
    const ClientID = '5894c01b-90de-4dac-8128-975df052aeee';
	const ClientSecret = "GqE_6R.TiU6K3-h81d-O536A2ao.1Y~-iP";
	const AuthURI = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
	const LoginURI = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
	const CampaignNamespace = 'https://bingads.microsoft.com/CampaignManagement/v13';
	const DeveloperToken = '1495NZ4GIX398829';
	const CampaignManagementAPI = 'https://campaign.api.bingads.microsoft.com/Api/Advertiser/CampaignManagement/v13/CampaignManagementService.svc?singleWsdl';
	const CustomerServiceNamespace = 'https://bingads.microsoft.com/Customer/v13';
	const CustomerManagementServiceAPI = 'https://clientcenter.api.bingads.microsoft.com/Api/CustomerManagement/v13/CustomerManagementService.svc?singleWsdl';

	static function BingAdsAuthentication($code, $redirect_uri)
	{	
		$curl = curl_init();
		$data = array('client_id' => self::ClientID,
		            'scope' => 'bingads.manage',
		            'code' => $code,
		            'grant_type' => 'authorization_code',
		            'redirect_uri' => $redirect_uri,
		            'client_secret' => self::ClientSecret
	            );

		curl_setopt_array($curl, array(
		  CURLOPT_URL => self::AuthURI,
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
            return TRUE;
		}else{
			$_SESSION['curl_error'] = $err;
			return FALSE;
		}
		
	}
	static function getNewAccessAndRefrehToken(){
		$curl = curl_init();
		$data = array('client_id' => self::ClientID,
		            'scope' => 'bingads.manage',
		            'code' => $_SESSION["code"],
		            'grant_type' => 'refresh_token',
		            'refresh_token' => $_SESSION["auth_data"]->refresh_token,
		            'client_secret' => self::ClientSecret
	            );

		curl_setopt_array($curl, array(
		  CURLOPT_URL => self::AuthURI,
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
			$_SESSION['auth_data'] = json_decode($response); 
		}else{
			return $err;
		}
	       
	}

	static function GetAdGroupsByCampaignId($AccountId, $CustomerId, $CampaignId){
        $campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);
		$request = new GetAdGroupsByCampaignIdRequest();

	    $request->CampaignId = $CampaignId;

	    return $campaign_proxy->GetAdGroupsByCampaignId($request);
	}

	static function getAccounts(){
		self::getNewAccessAndRefrehToken();
		$namespace = self::CustomerServiceNamespace;
		$headers = array();

		$headers[] = new SoapHeader(
			$namespace,
			'CustomerAccountId',
			''
		);

		$headers[] = new SoapHeader(
			$namespace,
			'CustomerId',
			''
		);

		$headers[] = new SoapHeader(
			$namespace,
			'DeveloperToken',
			self::DeveloperToken
		);

		$headers[] = new SoapHeader(
			$namespace,
			'UserName',
			''
		);

		$headers[] = new SoapHeader(
			$namespace,
			'Password',
			''
		);

		$headers[] = new SoapHeader(
			$namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);

		$proxy = @new SOAPClient(self::CustomerManagementServiceAPI);
		$proxy->__setSoapHeaders($headers);
		$getUserRequest = new GetUserRequest();
	    $getUserRequest->UserId = null;
		$user = $proxy->GetUser($getUserRequest)->User;

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

	    return $proxy->SearchAccounts($searchAccountsRequest)->Accounts;
	}

	static function AddCampaigns($AccountId, $CustomerId,
        $campaigns){
		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);
		$request = new AddCampaignsRequest();

        $request->AccountId = $AccountId;
        $request->Campaigns = $campaigns;

	    return $campaign_proxy->AddCampaigns($request);

	}

	static function AddAdGroups($AccountId, 
    	$CustomerId,
		$campaignId,
        $adGroups,
        $returnInheritedBidStrategyTypes){
		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new AddAdGroupsRequest();

        $request->CampaignId = $campaignId;
        $request->AdGroups = $adGroups;
        $request->ReturnInheritedBidStrategyTypes = $returnInheritedBidStrategyTypes;

        return $campaign_proxy->AddAdGroups($request);

	}

	static function AddAds($AccountId, 
    	$CustomerId,
    	$adGroupId,
        $ads){
		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new AddAdsRequest();

        $request->AdGroupId = $adGroupId;
        $request->Ads = $ads;

        return $campaign_proxy->AddAds($request);
	}

	static function AddKeywords($AccountId, 
    	$CustomerId,
        $adGroupId,
        $keywords,
        $returnInheritedBidStrategyTypes){
		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new AddKeywordsRequest();

        $request->AdGroupId = $adGroupId;
        $request->Keywords = $keywords;
        $request->ReturnInheritedBidStrategyTypes = $returnInheritedBidStrategyTypes;

        return $campaign_proxy->AddKeywords($request);

	}	
	static function GetCampaignsByAccountId($AccountId,
		$campaignType,
		$returnAdditionalFields)
	{
		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new GetCampaignsByAccountIdRequest();

		$request->AccountId = $AccountId;
		$request->CampaignType = $campaignType;
		$request->ReturnAdditionalFields = $returnAdditionalFields;

		return $campaign_proxy->GetCampaignsByAccountId($request);
	}
	static function UpdateCampaigns(
		$AccountId,
		$campaigns)
	{
		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);


		$request = new UpdateCampaignsRequest();

		$request->AccountId = $AccountId;
		$request->Campaigns = $campaigns;

		return $campaign_proxy->UpdateCampaigns($request);
	}

	static function GetKeywordsByAdGroupId($AccountId,
		$adGroupId)
	{
		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new GetKeywordsByAdGroupIdRequest();

		$request->AdGroupId = $adGroupId;

		return $campaign_proxy->GetKeywordsByAdGroupId($request);
	}

	static function DeleteKeywords($AccountId,
		$adGroupId,
		$keywordIds)
	{
		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new DeleteKeywordsRequest();

		$request->AdGroupId = $adGroupId;
		$request->KeywordIds = $keywordIds;
		return $campaign_proxy->DeleteKeywords($request);
	}
    
    static function AddCampaignCriterions($AccountId, $CustomerId,
        $campaignCriterions,
        $criterionType)
    {
        $campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);
        $request = new AddCampaignCriterionsRequest();

        $request->CampaignCriterions = $campaignCriterions;
        $request->CriterionType = $criterionType;

        return $campaign_proxy->AddCampaignCriterions($request);
    }

    static function GetGeoLocationsFileUrl($AccountId, $CustomerId)
	{

		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new GetGeoLocationsFileUrlRequest();

		$request->Version = '2.0';
		$request->LanguageLocale = 'en';

		return $campaign_proxy->GetGeoLocationsFileUrl($request);
	}

	static function GetCampaignCriterionsByIds($AccountId, $CustomerId,
		$campaignCriterionIds,
		$campaignId,
		$criterionType)
	{

		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new GetCampaignCriterionsByIdsRequest();

		$request->CampaignCriterionIds = $campaignCriterionIds;
		$request->CampaignId = $campaignId;
		$request->CriterionType = $criterionType;

		return $campaign_proxy->GetCampaignCriterionsByIds($request);
	}

	static function DeleteCampaignCriterions($AccountId, $CustomerId,
		$campaignCriterionIds,
		$campaignId,
		$criterionType)
	{

		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new DeleteCampaignCriterionsRequest();

		$request->CampaignCriterionIds = $campaignCriterionIds;
		$request->CampaignId = $campaignId;
		$request->CriterionType = $criterionType;

		return $campaign_proxy->DeleteCampaignCriterions($request);
	}

	static function UpdateAds($AccountId, $CustomerId,
		$adGroupId,
		$ads)
	{

		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new UpdateAdsRequest();

		$request->AdGroupId = $adGroupId;
		$request->Ads = $ads;

		return $campaign_proxy->UpdateAds($request);
	}

	static function DeleteAds($AccountId, $CustomerId,
		$adGroupId,
		$adIds)
	{

		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new DeleteAdsRequest();

		$request->AdGroupId = $adGroupId;
		$request->AdIds = $adIds;

		return $campaign_proxy->DeleteAds($request);
	}

	static function DeleteCampaigns($AccountId, $CustomerId,
		$campaignIds)
	{

		$campaign_header = array();
	    $campaign_namespace = self::CampaignNamespace;

	    $campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerAccountId',
			$AccountId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'CustomerId',
			$CustomerId
		);

		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'DeveloperToken',
			self::DeveloperToken
		);
		$campaign_header[] = new SoapHeader(
			$campaign_namespace,
			'AuthenticationToken',
			$_SESSION['auth_data']->access_token
		);
		$campaign_proxy = @new SOAPClient(self::CampaignManagementAPI);
		$campaign_proxy->__setSoapHeaders($campaign_header);

		$request = new DeleteCampaignsRequest();

		$request->AccountId = $AccountId;
		$request->CampaignIds = $campaignIds;

		return $campaign_proxy->DeleteCampaigns($request);
	}

}

?>