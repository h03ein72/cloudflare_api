<?php

namespace App\Repositories;

use App\Concerns\CurlHandler;
use App\Interfaces\CloudflareInterface;
use Exception;

class CloudflareRepository implements CloudflareInterface
{
    private $cloudflare_token = [];
    private $cloudflare;

    public function __construct()
    {
        $this->cloudflare_token[] = "X-Auth-Email: " . env('CLOUDFLARE_API_EMAIL');
        $this->cloudflare_token[] = "X-Auth-Key: ".env('CLOUDFLARE_API_KEY');
        $this->cloudflare = new CurlHandler($this->cloudflare_token);
    }
    /**
     * Post optional value
     * @param string $specify_domain Specify your domain, If you need extra info about it
     */
    public function getDomains(string $specify_domain = "")
    {
        try{
            $_response = $this->cloudflare->get("https://api.cloudflare.com/client/v4/zones/");
            $response = json_decode($_response);
            foreach ($response->result as $result ) {
                if($result->name == $specify_domain){
                    return $result;
                }
            }
            return $_response;
        }catch (Exception $error){
            return response()->json($error->getMessage());
        }
    }

    /**
     * Required values
     * @param $domain_name // Specify your domain, In order to get dns records
     * @param $type // Type of DNS
     * @param $name // DNS Name
     * @param $value // DNS Value
     * @param $proxified // False or True
     * @param $ttl // proxified ? Auto : (int)Second;
     * @param string $comment // Optional : Comments on request if necessary
     */
    public function setDns($domain_id, $type, $name, $value, $proxified, $ttl, string $comment = "")
    {
        try{
            $bodyContent = "{\"content\": \"{$value}\", \"name\": \"{$name}\", \"proxied\": {$proxified}, \"type\": \"{$type}\", \"comment\": \"{$comment}\", \"ttl\": {$ttl}}";
            return $this->cloudflare->post("https://api.cloudflare.com/client/v4/zones/{$domain_id}/dns_records", $bodyContent);
        }catch (Exception $error){
            return response()->json($error->getMessage());
        }
    }

    /**
     * Required values
     * @param $domain_id
     */
    public function getDns($domain_id)
    {
        try{
            return $this->cloudflare->get("https://api.cloudflare.com/client/v4/zones/{$domain_id}/dns_records");
        }catch (Exception $error){
            return response()->json($error->getMessage());
        }
    }

    /**
     * Required values
     * @param $domain_id // Specify your domain id
     * @param $dns_id // Specify your dns id
     * @param $type // Type of DNS
     * @param $name // DNS Name
     * @param $value // DNS Value
     * @param $proxified // False or True
     * @param $ttl // proxified ? Auto : (int)Second;
     * @param string $comment // Optional : Comments on request if necessary
     */
    public function editDns($domain_id, $dns_id, $type, $name, $value, $proxified, $ttl, string $comment = "")
    {
        try{
            $bodyContent = "{\"content\": \"{$value}\", \"name\": \"{$name}\", \"proxied\": {$proxified}, \"type\": \"{$type}\", \"comment\": \"{$comment}\", \"ttl\": {$ttl}}";
            return $this->cloudflare->update("https://api.cloudflare.com/client/v4/zones/{$domain_id}/dns_records/{$dns_id}", $bodyContent);
        }catch (Exception $error){
            return response()->json($error->getMessage());
        }
    }

    /**
     * Required values
     * @param $domain_id // Specify your domain identifier
     * @param $dns_id // Specify your DNS identifier
     */
    public function deleteDns($domain_id, $dns_id)
    {
        try{
            return $this->cloudflare->delete("https://api.cloudflare.com/client/v4/zones/{$domain_id}/dns_records/{$dns_id}");
        }catch (Exception $error){
            return response()->json($error->getMessage());
        }
    }

    /**
     * Required values
     * @param $account_id // Specify your cloudflare identifier
     * @param $domain_name // Specify your domain name
     */
    public function addDomain($account_id, $domain_name)
    {
        try{
            $bodyContent = "{\"account\": {\"id\": \"{$account_id}\"}, \"name\": \"{$domain_name}\", \"type\":\"full\"}";
            return $this->cloudflare->post("https://api.cloudflare.com/client/v4/zones", $bodyContent);
        }catch (Exception $error){
            return response()->json($error->getMessage());
        }
    }
}
