<?php
namespace devskyfly\robocmd;

trait DesignTrait
{

    //////////////////////////////////////////////////////////////////////////////////
    //Disign
    
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
    }

    public function designBuild()
    {
        $this->taskGulpRun('build')
        ->dir($this->designPath())
        ->run();
    }
}
