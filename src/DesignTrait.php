<?php
namespace devskyfly\robocmd;

/**
 * This is tool to interact with design in your project that lies in build path.
 * 
 * It depends on YiiTrait.
 */
trait DesignTrait
{

    //////////////////////////////////////////////////////////////////////////////////
    //Design
    
    public function designUpdate()
    {
        $this->designBuild();
        $this->designMv();
    }

    public function designMv()
    {
        $this->_copyDir($this->designJsPath(), $this->yiiFrontendPath().'/js');
        $this->_copyDir($this->designCssPath(), $this->yiiFrontendPath().'/css');
        $this->_copyDir($this->designImgPath(), $this->yiiFrontendPath().'/img');
        $this->_copyDir($this->designFontsPath(), $this->yiiFrontendPath().'/fonts');
        $this->designMvGrafic();
    }

    public function designBuild()
    {
        $this->taskGulpRun('build')
        ->dir($this->designPath())
        ->run();
    }

    protected function designPath()
    {
        return getcwd()."/builder";
    }

    protected function designJsPath()
    {
        return $this->designPath().'/build/app/js';
    }

    protected function designCssPath()
    {
        return $this->designPath().'/build/app/css';
    }

    protected function designImgPath()
    {
        return $this->designPath().'/build/app/img';
    }

    protected function designFontsPath()
    {
        return $this->designPath().'/build/app/fonts';
    }

    protected function designMvGrafic()
    {
        $buildPath = $this->designPath().'/build';
        $png = glob($buildPath.'/*.png');
        $xml = glob($buildPath.'/*.xml');
        $ico = glob($buildPath.'/*.ico');
        $svg = glob($buildPath.'/*.svg');
        $files = array_merge($png, $xml, $ico, $svg);
        $to = $this->yiiSrcPath().'/frontend/web';

        foreach ($files as $file) {
            $baseName = basename($file);
            $this->taskFilesystemStack()->copy($file, $to."/".$baseName)->run();
        }
    }
}
