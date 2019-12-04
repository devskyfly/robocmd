<?php
namespace devskyfly\robocmd;

trait AppTrait 
{

    //////////////////////////////////////////////////////////////////////////////////
    //App
    
    /**
     * Deploy application.
     * 
     * Deploy application by copy src path to versions path, exclude neaded files/dirs, and execute after deploy callback function.
     *
     * @param array $opts
     * @option $build "v1.0.0"
     */
    public function appDeploy($opts = ["build|b" => ""])
    {   
        if (empty($opts["build"])) {
            $this->say("Need app build name.");
            return -1;
        }

        //Check version path exists
        $versionPath = $this->appVersionsPath();
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
        $copyTask = $this->taskCopyDir([$this->appSrcPath() => $targetPath]);
        $exludeFiles = $this->appDeployExcludeFiles();
        
            //Exclude not neaded files and dirs
            if (!empty($exludeFiles)) {
                $copyTask->exclude($exludeFiles);
            };

        $copyTask->run();

        $this->taskFilesystemStack()->chmod($targetPath, 0775, 0000, true)->run();
        
        $this->appAfterDeployCallback($targetPath);
    }

    public function appVersionsPath($name = "")
    {
        if (empty($name)){
            return getcwd().'/../versions';
        } else {
            return getcwd().'/../'.$name;
        } 
    }

    /****************
     * User functions
     ***************/
    
    /**
     * Return current path were script was invoked. 
     *
     * @return void
     */
    public function appSrcPath()
    {
        return getcwd();
    }

    /**
     * User defined callback on deploy script finish.
     */
    public function appAfterDeployCallback($targetPath)
    {

    }

    /**
     * Can Redeclarate
     */
    public function appClear()
    {
        
    }

    /**
     * Return files/dirs list to exclude from deploy.
     * 
     * @return null | string[]
     */

    public function appDeployExcludeFiles()
    {
        return null;
    }

    /********************
     * End user functions
     *******************/

    
}