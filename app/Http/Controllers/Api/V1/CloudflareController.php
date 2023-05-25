<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CloudflareController extends Controller
{
    private \CurlHandle $CURL;
    private array $HTTPHEADER = [
        "Content-Type: application/json",
    ];

    public function __construct(){
        $this->HTTPHEADER[] = "X-Auth-Email: " . env('cloudflare_api_email');
        $this->HTTPHEADER[] = "X-Auth-Key: ".env('cloudflare_api_key');
        $this->CURL = curl_init();
    }

    private function curl_setopt_array_custom($curl_connection, array $_options){
//        $options = [
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 30,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_HTTPHEADER => $this->HTTPHEADER,
//        ];

//        curl_setopt_array($curl_connection, [
//            array_combine(
//                array_map("constant", array_keys($options)),
//                array_values($merge)
//            )
//        ]);
    }

    /*
     * Post optional value
     * [
     *  $specify_domain => Specify your domain, If you need extra info about it
     * ]
     */
    public function getDomains($specify_domain = null)
    {
        curl_setopt_array($this->CURL, [
           CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => "",
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 30,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "GET",
           CURLOPT_HTTPHEADER => $this->HTTPHEADER,
        ]);

        $response = curl_exec($this->CURL);
        $err = curl_error($this->CURL);

        if ($err){
            return $err;
        }else{
            $data = json_decode($response);
            $domains = [];
            foreach ($data->result as $domain)
                $domains[$domain->id] = $domain->name;

            if($specify_domain != null) {
                if(in_array($specify_domain, $domains)){
                    // must return the specified domain data
                    return array_search($specify_domain, $domains);
                }else{
                    // if not found the domain
                    return false;
                }
            }else{
                return response()->json($domains);
            }
        }
    }

    /*
     * Required values on POST
     * [
     *  domain_name => Specify your domain, In order to get dns records
     *  type => Type of DNS
     *  name => DNS Name
     *  value => DNS Value
     *  proxified => False or True
     *  ttl => proxified ? Auto : (int)Second;
     *  comment => Comments on commits if necessary
     * ]
     */
    public function setDns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_name' => 'required',
            'type' => 'required',
            'name' => 'required',
            'value' => 'required',
            'proxified' => 'required',
            'ttl' => 'required',
            'comment' => '',
        ]);

        if($validator->fails())
        {
            return response()->json(['All required params must be filled.']);
        }else
        {
            $domain = $validator->validated();
            $domain_id = $this->getDomains($domain['domain_name']);

            curl_setopt_array($this->CURL, [
                CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/{$domain_id}/dns_records",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\n  \"content\": \"{$domain['value']}\",\n  \"name\": \"{$domain['name']}\",\n  \"proxied\": {$domain['proxified']},\n  \"type\": \"{$domain['type']}\",\n  \"comment\": \"{$domain['comment']}\",\n  \"ttl\": {$domain['ttl']}\n}",
                CURLOPT_HTTPHEADER => $this->HTTPHEADER,
            ]);

            $response = curl_exec($this->CURL);
            $err = curl_error($this->CURL);
            return $err ? $err : $response;
        }
    }

    /*
     * Required values on POST
     * [
     *  domain_name => Specify your domain, In order to get dns records
     * ]
     */
    public function getDns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_name' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['Domain name is required!']);
        }else {
            $domain = $validator->validated();
            $domain_id = $this->getDomains($domain['domain_name']);

            curl_setopt_array($this->CURL, [
                CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/{$domain_id}/dns_records",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $this->HTTPHEADER,
            ]);

            $response = curl_exec($this->CURL);
            $err = curl_error($this->CURL);

            return $err ? $err : $response;
        }

    }

    /*
     * Required values on POST
     * [
     *  domain_name => Specify your domain
     *  dns_id => DNS Identifier
     *  type => Type of DNS
     *  name => DNS Name
     *  value => DNS Value
     *  proxified => False or True
     *  ttl => proxified ? Auto : (int)Second;
     *  comment => Comments on commits if necessary
     * ]
     */
    public function editDns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_name' => 'required',
            'dns_id' => 'required',
            'type' => 'required',
            'name' => 'required',
            'value' => 'required',
            'proxified' => 'required',
            'ttl' => 'required',
            'comment' => '',
        ]);

        if($validator->fails())
        {
            return response()->json(['Domain name and dns identifier are required!']);
        }else {
            $domain = $validator->validated();
            $domain_id = $this->getDomains($domain['domain_name']);

            curl_setopt_array($this->CURL, [
                CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/{$domain_id}/dns_records/{$domain['dns_id']}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PATCH",
                CURLOPT_POSTFIELDS => "{\n  \"content\": \"{$domain['value']}\",\n  \"name\": \"{$domain['name']}\",\n  \"proxied\": {$domain['proxified']},\n  \"type\": \"{$domain['type']}\",\n  \"comment\": \"{$domain['comment']}\",\n  \"ttl\": {$domain['ttl']}\n}",
                CURLOPT_HTTPHEADER => $this->HTTPHEADER,
            ]);

            $response = curl_exec($this->CURL);
            $err = curl_error($this->CURL);

            return $err ? $err : $response;
        }
    }

    /*
     * Required values on POST
     * [
     *  domain_name => Specify your domain
     *  dns_id => DNS Identifier
     * ]
     */
    public function deleteDns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_name' => 'required',
            'dns_id' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['Domain name and dns identifier are required!']);
        }else {
            $domain = $validator->validated();
            $domain_id = $this->getDomains($domain['domain_name']);

            curl_setopt_array($this->CURL, [
                CURLOPT_URL => "https://api.cloudflare.com/client/v4/zones/{$domain_id}/dns_records/{$domain['dns_id']}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => $this->HTTPHEADER,
            ]);

            $response = curl_exec($this->CURL);
            $err = curl_error($this->CURL);

            return $err ? $err : $response;
        }
    }
    public function __destruct()
    {
        curl_close($this->CURL);
    }
}
