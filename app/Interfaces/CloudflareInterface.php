<?php

namespace App\Interfaces;

interface CloudflareInterface
{
    public function getDomains(string $specify_domain = "");
    public function setDns($domain_id, $type, $name, $value, $proxified, $ttl, string $comment = "");
    public function getDns($domain_id);
    public function editDns($domain_id, $dns_id, $type, $name, $value, $proxified, $ttl, string $comment = "");
    public function deleteDns($domain_id, $dns_id);
    public function addDomain($account_id, $domain_name);
}
