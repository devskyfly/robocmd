<?php
require_once __DIR__."/../../../vendor/autoload.php";

use devskyfly\robocmd\YiiTrait;

class RoboFile extends \Robo\Tasks
{
    use YiiTrait;
    // define public methods as commands

    public function yiiDeployExclude()
    {
        return [
            'exclude'
        ];
    }

    public function yiiEnv()
    {
        return ["DigitalOcean"];
    }
}