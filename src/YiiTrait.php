<?php
namespace devskyfly\robocmd;

trait YiiTrait 
{
    //////////////////////////////////////////////////////////////////////////////////
    //Yii
    
    /**
     * Can Redeclarate
     */
    public function yiiDeployCallBack()
    {

    }

    /**
     * Can Redeclarate
     */
    public function yiiClear()
    {
        
    }

    /**
     * Can Redeclarate
     */

    public function yiiDeployExclude()
    {
        return null;
    }

    protected function yiiFrontendPath()
    {
        return getcwd()."/frontend/web";
    }

    public function yiiVersionsPath()
    {
        return getcwd().'/../versions';
    }

    public function yiiSrcPath()
    {
        return getcwd();
    }

    public function yiiDeploy(array $args)
    {   
        if (!isset($args[0])) {
            $this->say("Need app version in args.");
            return -1;
        }

        $versionPath = $this->yiiVersionsPath();
        $projectPath = $versionPath.'/'.$args[0];

        if (!file_exists($versionPath)) {
            $this->taskFilesystemStack()
            ->mkdir($versionPath)
            ->run();
        }

        if (file_exists($projectPath)) {
            $this->io()->title("Directory {projectPath} is already exists.");
            if ($this->confirm("Do you want to clear it?")) {
                $this->_deleteDir($projectPath);
            } else {
                $this->say("Task Terminated.");
                return -1;
            }
        }

        $this->taskCopyDir([$this->yiiSrcPath() => $projectPath])
        ->exclude($this->yiiDeployExclude())
        ->run();

        if(!isset($args[1])) {
            $env = "Production";
        } else{
            $env = $args[1];
        }

        $this->taskExec($projectPath."/init --env={$env} --overwrite=All")
        ->run();
        $this->taskFilesystemStack()->chmod($projectPath, 0775, 0000, true)->run();
        $this->taskComposerInstall()->dir($projectPath)->run();
        $this->yiiDeployCallBack($projectPath);
    }

    public function yiiClearQueue()
    {
        $this->taskExec('./yii queue/clear')->run();
    }
}