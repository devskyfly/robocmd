<?php
require_once __DIR__."/../../../vendor/autoload.php";

use devskyfly\robocmd\YiiTrait;
use devskyfly\robocmd\AppTrait;
use devskyfly\robocmd\DevTestTrait;


class RoboFile extends \Robo\Tasks
{
    use YiiTrait;
    use AppTrait;
    use DevTestTrait;

    // define public methods as commands

    //Yii redeclaration
    public function yiiDeployExcludeFiles()
    {
        return [
            'exclude'
        ];
    }

    public function yiiEnv()
    {
        return ["DigitalOcean"];
    }

    //App redeclaration

    public function appDeployExcludeFiles()
    {
        return [
            'exclude'
        ];
    }
}