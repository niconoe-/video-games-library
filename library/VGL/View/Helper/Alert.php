<?php

/**
 * Class Alert
 * @package VGL\View\Helper
 */
class VGL_View_Helper_Alert extends \Zend_View_Helper_Abstract
{

    private $_content = null;

    /**
     * @param $msg
     * @return $this
     */
    public function alert($msg)
    {
        $this->_content = (string)$msg;
        return $this;
    }

    /**
     * @return string
     */
    public function info()
    {
        return $this->_buildContent(__FUNCTION__);
    }

    /**
     * @return string
     */
    public function success()
    {
        return $this->_buildContent(__FUNCTION__);
    }

    /**
     * @return string
     */
    public function warning()
    {
        return $this->_buildContent(__FUNCTION__);
    }

    /**
     * @return string
     */
    public function danger()
    {
        return $this->_buildContent(__FUNCTION__);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->info();
    }

    /**
     * @param $type
     * @return string
     */
    protected function _buildContent($type)
    {
        return '<div class="alert alert-' . $type . '" role="alert">' . $this->_content . '</div>';
    }
}