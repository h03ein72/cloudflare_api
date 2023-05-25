<?php

const EMAIL = "hosseinvahabi@ymail.com";
const TOKEN = "be456ee9bddb895a55f624906ee6f99e9308d";
const HTTPHEADER = [
    "Content-Type: application/json",
    "X-Auth-Email: ".EMAIL,
    "X-Auth-Key: ".TOKEN
];

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => HTTPHEADER,
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $data = json_decode($response);
    foreach ($data->result as $key => $value){

        // check if entered domain exists
        // insert dns
        if($value->name){
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/".$value->id."/dns_records",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n  \"content\": \"198.51.100.4\",\n  \"name\": \"example.com\",\n  \"proxied\": false,\n  \"type\": \"A\",\n  \"comment\": \"Domain verification record\",\n  \"tags\": [\n    \"owner:dns-team\"\n  ],\n  \"ttl\": 3600\n}",
                CURLOPT_HTTPHEADER => HTTPHEADER,
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                echo $response;
            }
        }
    }
}