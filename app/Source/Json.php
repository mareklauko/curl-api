<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 24/04/2019
 * Time: 17:48
 */
namespace App\Source;
use Exception,
    \App\Config\Credentials as Credentials;
class Json {

    private $connection;
    private $url;
    private $user;
    private $pass;

   public function __construct (Credentials $credentials)
    {
        $credentials = $credentials->getCredentials();
        $this->user = $credentials['username'];
        $this->pass = $credentials['password'];
        $this->server = $credentials['server'];
    }

    // req = POST/ GET
    protected function doRequest($req, $path, $request = null) : array
    {
        if (strpos($path, $this->url) === 0) {
            $path = substr($path, strlen($this->url) - 1);
        }

        $curl = $this->getConnection();
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $req);
        curl_setopt($curl, CURLOPT_URL, $this->url . $path);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: Websupport PHP Library',
        ]);
        if ($this->user !== null && $this->pass !== null) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $this->user . ':' . $this->pass);
        }

        if (!empty($request)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpStatus == 0) {
            $errorMessage = curl_error($curl);
            throw new Exception($errorMessage);
        }

        return [$response, $httpStatus];
    }

    public function getRequest($path, $params = null) : array
    {
        list ($encoded, $response) = $this->doRequest('GET', $path, $params !== null ? json_encode($params) : null);
        if ($response === 200) {
            return $this->getJson($encoded);
        } else {
            throw new Exception($response . ' || ' . $this->getJson($encoded));
        }
    }

    public function postRequest($path, $params = null) : array
    {
        list ($encoded, $response) = $this->doRequest('POST', $path, json_encode($params));
        if ($response === 200 || $response === 201 || $response === 422 || $response === 400) {
            return $this->getJson($encoded);
        } else {
            throw new Exception($response . ' || ' . $this->getJson($encoded));
        }
    }

    protected function setConnection() : void
    {
        $curl = curl_init();

        $this->connection = $curl;
    }

    protected function getConnection()
    {
        if(empty($this->connection)) {
            $this->setConnection();
        }

        return $this->connection;
    }

    protected function getJson($encoded) : array
    {
        $json = [];
        try {
            $json = json_decode($encoded);
        }
        catch (Exception $e) {
            throw new Exception($e);
        }

        return $json;
    }
}