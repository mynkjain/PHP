<?php

try{
$soapclient = new SoapClient("http://webservices.oorsprong.org/websamples.countryinfo/CountryInfoService.wso?WSDL");
$response =$soapclient->__soapCall("CountryName", array("IND"));
echo "<pre>";
print_r($response);
}catch(Exception $e){
	echo $e->getMessage();
}

?>