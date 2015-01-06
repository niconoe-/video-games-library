<?php

/**
 * Class VGL_WebServices_Platform
 * @package VGL_WebServices
 */
class VGL_WebServices_Platform
    extends VGL_WebServices
    implements VGL_WebServices_Interface
{

    const SERVICE = 'GetPlatform';

    /**
     * @internal param string $name
     * @internal param string $platform
     * @internal param string $genre
     * @return VGL_WebServices_Platform
     */
    public static function get()
    {
        $aArgs = func_get_args();
        if (empty($aArgs)) {
            throw new BadMethodCallException('Argument 1 (name) is missing.');
        }
        list($id) = $aArgs;
        $xmlResult = self::_getCacheValue(self::SERVICE, $id);
        if ($xmlResult === null) {
            return null;
        }
        if ($xmlResult === false) {
            $aArgs = [];
            !empty($id) ? $aArgs['id'] = $id : null;
            $xmlResult = self::_callService(self::SERVICE, $aArgs);
            VGL_Cache::getInstance()->setCurrentTags('Platform');
            $xmlResult = self::_setCacheValue($xmlResult, self::SERVICE, $id);
            VGL_Cache::getInstance()->resetCurrentTags();
        }

        $oXml = new DOMDocument('1.0', 'UTF-8');
        $oXml->loadXML($xmlResult);
        return new self($oXml);
    }

    public function parse()
    {
        $xPath = new DOMXPath($this->oXml);
        $sBasePlatformUrl = $xPath->query('/Data/baseImgUrl')->item(0)->nodeValue;
        $aInfoPlatform = [];
        foreach ($xPath->query('/Data/Platform/*') as $oNode) {
            ($oNode->nodeName === 'id' ? $aInfoPlatform['id'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'Platform' ? $aInfoPlatform['name'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'overview' ? $aInfoPlatform['overview'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'developer' ? $aInfoPlatform['developer'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'manufacturer' ? $aInfoPlatform['manufacturer'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'cpu' ? $aInfoPlatform['cpu'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'memory' ? $aInfoPlatform['memory'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'graphics' ? $aInfoPlatform['graphics'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'sound' ? $aInfoPlatform['sound'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'display' ? $aInfoPlatform['display'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'media' ? $aInfoPlatform['media'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'maxcontrollers' ? $aInfoPlatform['maxController'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'youtube' ? $aInfoPlatform['youtube'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'Rating' ? $aInfoPlatform['rating'] = round($oNode->nodeValue, 6) : null);
            $oNode->nodeName === 'Images'
                ? $aInfoPlatform['Images'] = self::parseImages($sBasePlatformUrl, $oNode)
                : null;
        }
        //Run html_entity_decode for all fields that is not an array
        foreach ($aInfoPlatform as &$value) {
            if (is_array($value)) {
                continue;
            }
            $value = html_entity_decode($value, ENT_QUOTES);
        } unset($value);
        return $aInfoPlatform;
    }

    public function isPlatform()
    {
        return (count((array)$this->oXml->getElementsByTagName('Platform')) > 0);
    }

}