<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 24/04/2019
 * Time: 18:16
 */
namespace App;
use Exception,
    App\Config\Credentials as Credentials,
    App\Source\Json as Json,
    App\Model\Detail as Detail,
    App\Model\Group as Group;
final class App {
    /* services */
    public $service;

    public function __construct()
    {
        // json with Credentials class
        $this->service['json'] = new Json(new Credentials());
        $this->service['group'] = new Group($this->service['json']);
        $this->service['detail'] = new Detail($this->service['json']);
    }
}