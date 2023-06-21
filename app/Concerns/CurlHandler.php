<?php
namespace App\Concerns;

class CurlHandler{
    private $curl;
    private $httpheader = [
        "Content-Type: application/json",
    ];

    public function __construct($tokens = []){
        foreach ($tokens as $token) $this->httpheader[] = $token;
        $this->curl = curl_init();
    }

    public function get($path){
        return $this->custom_curl($path);
    }
    public function post($path, $data){
        return $this->custom_curl($path, "post", $data);
    }

    public function update($path, $data){
        return $this->custom_curl($path, "update", $data);
    }

    public function delete($path){
        return $this->custom_curl($path, "delete");
    }

    protected function custom_curl ($path, $type = "", $data = [])
    {
        $default_options = [
            CURLOPT_URL => $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
        ];

        curl_setopt_array($this->curl, $default_options);

        if($type == "post"){
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }elseif($type == "update"){
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }elseif($type == "delete"){
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->httpheader);

        $response = curl_exec($this->curl);
        $err = curl_error($this->curl);

        return $response;
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }
}
