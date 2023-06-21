<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Interfaces\CloudflareInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CloudflareController extends Controller
{
    private CloudflareInterface $repository;

    public function __construct(CloudflareInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all available domains from cloudflare
     * @param Request $request
     */
    public function getDomains(Request $request)
    {
        return empty($request->get('domain_name')) ? $this->repository->getDomains() : $this->repository->getDomains($request->get('domain_name'));
    }

    /**
     * Set DNS record to specified domain
     * @param Request $request
     * @throws ValidationException
     */
    public function setDns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_id' => 'required',
            'type' => 'required',
            'name' => 'required',
            'value' => 'required',
            'proxified' => 'required',
            'ttl' => 'required',
            'comment' => '',
        ]);

        if($validator->fails())
        {
            return response()->json(['Error' => 'All required params must be filled.']);
        }else
        {
            $data = $validator->validated();
            if (empty($data['comment'])) $data['comment'] = "";
            return $this->repository->setDns($data['domain_id'], $data['type'], $data['name'], $data['value'], $data['proxified'], $data['ttl'], $data['comment']);
        }
    }

    /**
     * Get DNS record of specified domain
     * @param Request $request
     * @throws ValidationException
     */
    public function getDns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_id' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['Error' => 'Domain id is required!']);
        }else {
            $data = $validator->validated();
            return $this->repository->getDns($data['domain_id']);
        }

    }

    /**
     * Update any specified DNS
     * @param Request $request
     * @throws ValidationException
     */
    public function editDns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_id' => 'required',
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
            return response()->json(['Error' => 'Domain id and dns identifier are required!']);
        }else {
            $data = $validator->validated();
            if (empty($data['comment'])) $data['comment'] = "";
            return $this->repository->editDns($data['domain_id'], $data['dns_id'] ,$data['type'], $data['name'], $data['value'], $data['proxified'], $data['ttl'], $data['comment']);
        }
    }

    /**
     * Delete any specified DNS
     * @param Request $request
     * @throws ValidationException
     */
    public function deleteDns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain_id' => 'required',
            'dns_id' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['Error' => 'Domain id and dns identifier are required!']);
        }else {
            $data = $validator->validated();
            return $this->repository->deleteDns($data['domain_id'], $data['dns_id']);
        }
    }

    /**
     * Add domain to cloudflare
     * @param Request $request
     * @throws ValidationException
     */
    public function addDomain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required',
            'domain_name' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['Error' => 'All required params must be filled.']);
        }else
        {
            $data = $validator->validated();
            return $this->repository->addDomain($data['account_id'], $data['domain_name']);
        }
    }
}
