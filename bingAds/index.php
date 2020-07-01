<?php
ini_set('max_execution_time', 1200);

require_once "./Developer/BingAdsHelper.php";
//Developer classes
use Developer\BingAds\BingAdsHelper;

$client_id = BingAdsHelper::ClientID;
$login_uri = BingAdsHelper::LoginURI;
$redirect_uri = 'http://localhost/bingAds/OAuth2Callback.php';

header('Location: '. $login_uri.'?scope=bingads.manage&prompt=login&client_id='.$client_id.'&response_type=code&redirect_uri='.$redirect_uri);
die;
?>