<?php
namespace devskyfly\robocmd;

trait YiiTrait 
{
    //////////////////////////////////////////////////////////////////////////////////
    //Yii
    
    public function devTest()
    {

    }

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

    /**
     * Return array about additional env array.
     *
     * @return []
     */
    public function yiiEnv()
    {
        return [];
    }

    /**
     * Deploy yii application.
     * 
     * Deploy yii application by copy src path to versions path.
     *
     * @param string args[0] - app version 
     * --env string ["Production", "Development"]
     */
    public function yiiDeploy(array $args, $opt = ["env|e" => "Production"])
    {   
        $env = ["Production", "Development"];
        $env = array_merge($env, $this->yiiEnv());

        if (!isset($args[0])) {
            $this->say("Need app version in args.");
            return -1;
        }

        if (!in_array($opt["env"], $env)) {
            throw new \OutOfRangeException("Value \"{$opt['env']}\" out of env array.");
        }

        $versionPath = $this->yiiVersionsPath();
        $projectPath = $versionPath.'/'.$args[0];

        if (!file_exists($versionPath)) {
            $this->taskFilesystemStack()
            ->mkdir($versionPath)
            ->run();
        }

        if (file_exists($projectPath)) {
            $this->io()->title("Directory \"{$projectPath}\" is already exists.");
            if ($this->confirm("Do you want to clear it?")) {
                $this->_deleteDir($projectPath);
            } else {
                $this->say("Task was terminated.");
                return -1;
            }
        }

        $this->taskCopyDir([$this->yiiSrcPath() => $projectPath])
        ->exclude($this->yiiDeployExclude())
        ->run();

        $this->taskExec($projectPath."/init --env={$env} --overwrite=All")
        ->run();
        $this->taskFilesystemStack()->chmod($projectPath, 0775, 0000, true)->run();
        $this->taskComposerInstall()
        ->noDev()
        ->dir($projectPath)->run();
        $this->yiiDeployCallBack($projectPath);
    }

}