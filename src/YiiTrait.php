<?php
namespace devskyfly\robocmd;

trait YiiTrait 
{
    //////////////////////////////////////////////////////////////////////////////////
    //Yii
    
    /****************
     * User functions
     ***************/
    
    /**
     * Return current path were script was invoked. 
     *
     * @return void
     */
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
     * User defined callback on deploy script finish.
     */
    public function yiiAfterDeployCallback($projectPath)
    {

    }

    /**
     * Can Redeclarate
     */
    public function yiiClear()
    {
        
    }

    /**
     * Return files/dirs list to exclude from deploy.
     * 
     * @return null | string[]
     */

    public function yiiDeployExcludeFiles()
    {
        return null;
    }

    /********************
     * End user functions
     *******************/

    
    

    /**
     * Deploy yii application.
     * 
     * Deploy yii application by copy src path to versions path, exclude neaded files/dirs, and execute after deploy callback function.
     *
     * --env string ["Production", "Development"]
     * --build string "v1.0.0"
     */
    public function yiiDeploy($opt = ["env|e" => "Production", "build|b" => ""])
    {   
        $env = ["Production", "Development"];
        $env = array_merge($env, $this->yiiEnv());

        if (empty($opt["build"])) {
            $this->say("Need app build name.");
            return -1;
        }

        if (!in_array($opt["env"], $env)) {
            throw new \OutOfRangeException("Value \"{$opt['env']}\" out of env array.");
        }

        $versionPath = $this->yiiVersionsPath();
        $projectPath = $versionPath.'/'.$opt["build"];

        if (!file_exists($versionPath)) {
            $this->taskFilesystemStack()
            ->mkdir($versionPath)
            ->run();
        }

        if (file_exists($projectPath)) {
            $this->io()->title("Directory \"{$projectPath}\" is already exists.");
            if ($this->confirm("Do you want to clear it")) {
                $this->_deleteDir($projectPath);
            } else {
                $this->say("Task was terminated.");
                return -1;
            }
        }

        $copyTask = $this->taskCopyDir([$this->yiiSrcPath() => $projectPath]);
        $exludeFiles = $this->yiiDeployExcludeFiles();
        
        if (!empty($exludeFiles)) {
            $copyTask->exclude($exludeFiles);
        };

        $copyTask->run();

        $this
        ->taskExec($projectPath."/init --env={$opt["env"]} --overwrite=All")
        ->run();

        $this->taskFilesystemStack()->chmod($projectPath, 0775, 0000, true)->run();
        
        $this->taskComposerInstall()
        ->noDev()
        ->dir($projectPath)->run();

        $this->yiiAfterDeployCallback($projectPath);
    }

    protected function yiiFrontendPath()
    {
        return getcwd()."/frontend/web";
    }

    public function yiiVersionsPath()
    {
        return getcwd().'/../versions';
    }
}