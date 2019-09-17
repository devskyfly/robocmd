<?php
namespace devskyfly\robocmd;

trait GitTrait 
{
    //////////////////////////////////////////////////////////////////////////////////
    //Git

    public function gitCommit($opt = ["index|i" => false])
    {
        if ($opt["index"]) {
            $this->gitAdd();
        }

        $this->taskExec('git commit')->dir(getcwd())->run();
    }

    public function gitAdd(array $args)
    {
        if(empty($args)){
            $args = ["."];
        }

        $args = implode(" ", $args);
        $this->yiiClear();
        $this->taskExec('git add '.$args)
        ->dir(getcwd())
        ->run();
    }
}