<?php
namespace devskyfly\robocmd;

trait YiiTrait 
{
    /**
     * Deploy yii application.
     * 
     * Deploy yii application by copy src path to versions path, exclude neaded files/dirs, and execute after deploy callback function.
     *
     * @param array $opts
     * @option $env "Production"|"Development"|...
     * @option $build "v1.0.0"
     */
    public function yiiDeploy($opts = ["env|e" => "Production", "build|b" => ""])
    {   
        if (empty($opts["build"])) {
            $this->say("Need app build name.");
            return -1;
        }

        // Check environment
        $env = ["Production", "Development"];
        $env = array_merge($env, $this->yiiEnv());

        if (!in_array($opts["env"], $env)) {
            throw new \OutOfRangeException("Value \"{$opts['env']}\" out of env array.");
        }

        //Check version path exists
        $versionPath = $this->yiiVersionsPath();
        $targetPath = $versionPath.'/'.$opts["build"];

        if (!file_exists($versionPath)) {
            $this->taskFilesystemStack()
            ->mkdir($versionPath)
            ->run();
        }

        //Check target path exists
        if (file_exists($targetPath)) {
            $this->io()->title("Directory \"{$targetPath}\" is already exists.");
            if ($this->confirm("Do you want to clear it")) {
                $this->_deleteDir($targetPath);
            } else {
                $this->say("Task was terminated.");
                return -1;
            }
        }

        //Copy src to target
        $copyTask = $this->taskCopyDir([$this->yiiSrcPath() => $targetPath]);
        $exludeFiles = $this->yiiDeployExcludeFiles();
        
            //Exclude not neaded files and dirs
            if (!empty($exludeFiles)) {
                $copyTask->exclude($exludeFiles);
            };

        $copyTask->run();

        // Yii install environment
        $this
        ->taskExec($targetPath."/init --env={$opts["env"]} --overwrite=All")
        ->run();

        $this->taskFilesystemStack()->chmod($targetPath, 0775, 0000, true)->run();
        
        // Composer install no dev
        $this->taskComposerInstall()
        ->noDev()
        ->dir($targetPath)->run();

        $this->yiiAfterDeployCallback($targetPath);
    }

    protected function yiiFrontendPath()
    {
        return getcwd()."/frontend/web";
    }

    public function yiiVersionsPath($name = "")
    {
        if (empty($name)){
            return getcwd().'/../versions';
        } else {
            return getcwd().'/../'.$name;
        }
    }

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
    public function yiiAfterDeployCallback($targetPath)
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
}