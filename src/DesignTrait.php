<?php
namespace devskyfly\robocmd;

trait DesignTrait
{
    //////////////////////////////////////////////////////////////////////////////////
    //Git

    public function gitCommit($opt = ["index|i" => false])
    {
        if ($opt["index"]) {
            $this->gitAdd();
        }

        $this->taskExec('git commit')->run();
    }

    public function gitAdd($args=["."])
    {
        $this->yiiClear();
        $this->taskExec('git add')->args($args)->run();
    }
}
