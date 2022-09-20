<?php

// Server domain or server ip

$server = "test.createc-la.net";

 

//Local WSDL File (in this sample we are using SE Administration WebService)

//$wsdl     = "adm_ws.wsdl";

//URL to download a remote WSDL

$wsdl     = "https://$server/se/ws/adm_ws.php?wsdl";
 

//endpoint to connect

$location = "https://$server/apigateway/se/ws/adm_ws.php";

$userToken="eyJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2NjM2OTQ5MTYsImV4cCI6MTc2NzIyNTU0MCwiaWRsb2dpbiI6Indsb3BleiIsInJhdGVsaW1pdCI6MTIwLCJxdW90YWxpbWl0IjoxMDAwMDB9.eyhtnzR25vbmHGyfSj40dPbYdtN8E-B03Ha1Rx4vAWI";

//Http request context (you should set the Authorization Token). In context details you can disable ssl feature (https://www.php.net/manual/pt_BR/context.ssl.php)

$context = array(

   'http' => array(

       'header' => 'Authorization: ' . $userToken //user token catured in your sesuite account details

   ));

 

 

$client = new SoapClient($wsdl,array(

 "trace" => 1, // enable trace

 "exceptions" => 1, // enable exceptions

 "stream_context" => stream_context_create($context),

 "location" => $location

));

 

//below we are using a sample method in SE Administration WebService

$return = $client->editPosition(array(

 'ID' => "SamplePosition",

 'DESC' => "SamplePosition"

));

echo $return;