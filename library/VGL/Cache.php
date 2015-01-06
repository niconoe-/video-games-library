<?php

/**
 * Class View
 * @package Cache
 */
class VGL_Cache
{
    private static $_instance = null;
    private $_oCache = null;
    private $_aCurrentTags = [];

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function getCache()
    {
        return self::getInstance()->_oCache;
    }

    public function getCurrentTags()
    {
        return $this->_aCurrentTags;
    }

    public function setCurrentTags($aTags)
    {
        if (empty($aTags)) {
            return $this;
        }
        if (!is_array($aTags)) {
            $aTags = [$aTags];
        }
        $this->_aCurrentTags = $aTags;
        return $this;
    }

    public function resetCurrentTags()
    {
        $this->_aCurrentTags = [];
        return $this;
    }

    private function __construct()
    {
        $frontendOptions = [
            'automatic_serialization' => true,
            'lifetime' => 84600, //1 day
        ];
        $backendOptions = [
            'cache_dir' => APPLICATION_PATH . '/../data/cache',
        ];
        $this->_oCache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        return $this;
    }

    public static function save($value, $id, $lifetime = null)
    {
        return self::getCache()->save($value, $id, self::getInstance()->getCurrentTags(), $lifetime);
    }
}