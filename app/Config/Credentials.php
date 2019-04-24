<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 24/04/2019
 * Time: 18:13
 */
namespace App\Config;
class Credentials {

    private $credentials = null;

    private function setCredentials() : void
    {
        // TODO: maybe load from neon ....
        $this->credentials = [
            'username' => '',
            'password' => '',
            'server' => ''
        ];
    }

    public function getCredentials() : array
    {
        if (empty($credentials)) {
            $this->setCredentials();
        }

        return $this->credentials;
    }
}