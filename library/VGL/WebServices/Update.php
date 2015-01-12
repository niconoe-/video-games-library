<?php

/**
 * Class VGL_WebServices_Update
 * @package VGL_WebServices
 */
class VGL_WebServices_Update
    extends VGL_WebServices
    implements VGL_WebServices_Interface
{

    const SERVICE = 'Updates';

    /**
     * @param string ...$time
     * @return VGL_WebServices_Update
     */
    public static function get()
    {
        $aArgs = func_get_args();
        if (empty($aArgs)) {
            throw new BadMethodCallException('Argument 1 (time) is missing.');
        }
        list($time) = $aArgs;
        $aArgs = [];
        !empty($time) ? $aArgs['time'] = $time : null;
        $xmlResult = self::_callService(self::SERVICE, $aArgs);

        $oXml = new DOMDocument('1.0', 'UTF-8');
        $oXml->loadXML($xmlResult);
        return new self($oXml);
    }

    public function parse()
    {
        $xPath = new DOMXPath($this->oXml);
        $aUpdateGames = [];
        foreach ($xPath->query('/Items/Game') as $oNode) {
            $aUpdateGames[] = $oNode->nodeValue;
        }

        return $aUpdateGames;
    }

}