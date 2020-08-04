<?php

$limit = $_GET["start"];
$currency = $_GET["convert"];

$url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
if (isset($url)) {

    $parameters = ["start" => "{$limit}", "limit" => 1, "convert" => "{$currency}"];
    $headers = ['Accepts: application/json', 'X-CMC_PRO_API_KEY: 32d63758-c31a-48d0-8295-02cd237dcfaa'];

    $qs = http_build_query($parameters); // query string encode the parameters
    $request = "{$url}?{$qs}"; // create the request URL
    $curl = curl_init(); // Get cURL resource
    curl_setopt_array($curl, array(CURLOPT_URL => $request, CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => 1));
    $response = curl_exec($curl); // Send the request, save the response
    print_r(json_decode($response)); // print json decoded response

    curl_close($curl); // Close request
}

?>
