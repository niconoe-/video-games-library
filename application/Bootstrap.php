<?php

/**
 * Class Bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);
    }

    protected function _initHeadTitle()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->headTitle('Video Games Library', 'SET')->setSeparator(' - ');
    }
}

