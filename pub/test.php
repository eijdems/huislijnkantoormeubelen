<?php 
   // echo file_get_contents("http://ione.huislijnkantoormeubelen.nl:8888/webservice/web_services.test");

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.cutlistoptimizer.com/oauth/token',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_SSL_VERIFYPEER => FALSE,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('username' => 'eijdem.internet@gmail.com','password' => 'Dark02dark02!cutlist','grant_type' => 'password'),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic Y3V0bGlzdG9wdGltaXplcjpjdXRsaXN0b3B0aW1pemVy'
  ),
));

$response = curl_exec($curl);
var_dump($response);
curl_close($curl);
echo $response;
?>
