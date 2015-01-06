<?php

/**
 * Class VGL_WebServices_PlatformsList
 * @package VGL_WebServices
 */
class VGL_WebServices_PlatformsList
    extends VGL_WebServices
    implements VGL_WebServices_Interface
{

    const SERVICE = 'GetPlatformsList';

    /**
     * @internal param string $name
     * @internal param string $platform
     * @internal param string $genre
     * @return VGL_WebServices_PlatformsList
     */
    public static function get()
    {
        $xmlResult = self::_getCacheValue(self::SERVICE);
        if ($xmlResult === null) {
            return null;
        }
        if ($xmlResult === false) {
            $xmlResult = self::_callService(self::SERVICE);
            $xmlResult = self::_setCacheValue($xmlResult, self::SERVICE);
        }

        $oXml = new DOMDocument('1.0', 'UTF-8');
        $oXml->loadXML($xmlResult);
        return new self($oXml);
    }

    public function parse()
    {
        $aXmlPlatforms = $this->oXml->getElementsByTagName('Platform');
        $aPlatforms = [];
        foreach ($aXmlPlatforms as $oXmlPlatform) {
            $aPlatforms[] = $oXmlPlatform->getElementsByTagName('id')->item(0)->nodeValue;
        }
        return $aPlatforms;
    }

}