<?php

/**
 * Class Action
 * @property VGL_View $view
 */
class VGL_Controller_Action extends \Zend_Controller_Action
{

    public function init()
    {
        $this->view->headLink()->appendStylesheet('/css/bootstrap.min.css');
        $this->view->headLink()->appendStylesheet('/css/bootstrap-theme.min.css');
        $this->view->headScript()->appendFile('/js/bootstrap.min.js');

        $sControllerName = $this->getRequest()->getControllerName();
        $sActionName = $this->getRequest()->getActionName();

        //CSS part
        $sFilename = APPLICATION_PATH . '/../public/css/' . $sControllerName . '/' . $sActionName . '.css';
        $sRealFilename = realpath($sFilename);
        if ($sRealFilename !== false) {
            $this->view->headLink()->appendStylesheet('/css/' . $sControllerName . '/' . $sActionName . '.css');
        }

        //JS part
        $sFilename = APPLICATION_PATH . '/../public/js/' . $sControllerName . '/' . $sActionName . '.js';
        $sRealFilename = realpath($sFilename);
        if ($sRealFilename !== false) {
            $this->view->headScript()->appendFile('/js/' . $sControllerName . '/' . $sActionName . '.js');
        }
    }


    /**
     * Initialize View object
     *
     * Initializes {@link $view} if not otherwise a Zend_View_Interface.
     *
     * If {@link $view} is not otherwise set, instantiates a new Zend_View
     * object, using the 'views' subdirectory at the same level as the
     * controller directory for the current module as the base directory.
     * It uses this to set the following:
     * - script path = views/scripts/
     * - helper path = views/helpers/
     * - filter path = views/filters/
     *
     * @return \Zend_View_Interface
     * @throws \Zend_Controller_Exception if base view directory does not exist
     */
    public function initView()
    {
        if (!$this->getInvokeArg('noViewRenderer') && $this->_helper->hasHelper('viewRenderer')) {
            return $this->view;
        }

        if (isset($this->view) && ($this->view instanceof \Zend_View_Interface)) {
            return $this->view;
        }

        $request = $this->getRequest();
        $module  = $request->getModuleName();
        $dirs    = $this->getFrontController()->getControllerDirectory();
        if (empty($module) || !isset($dirs[$module])) {
            $module = $this->getFrontController()->getDispatcher()->getDefaultModule();
        }
        $baseDir = dirname($dirs[$module]) . DIRECTORY_SEPARATOR . 'views';
        if (!file_exists($baseDir) || !is_dir($baseDir)) {
            throw new \Zend_Controller_Exception('Missing base view directory ("' . $baseDir . '")');
        }

        $this->view = new VGL_View(['basePath' => $baseDir]);
        return $this->view;
    }
}