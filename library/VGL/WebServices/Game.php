<?php

/**
 * Class VGL_WebServices_Game
 * @package VGL_WebServices
 */
class VGL_WebServices_Game
    extends VGL_WebServices
    implements VGL_WebServices_Interface
{

    const SERVICE = 'GetGame';

    /**
     * @param int ...$id
     * @return VGL_WebServices_Game
     */
    public static function get()
    {
        $aArgs = func_get_args();
        if (empty($aArgs)) {
            throw new BadMethodCallException('Argument 1 (id) is missing.');
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
            $xmlResult = self::_setCacheValue($xmlResult, self::SERVICE, $id);
        }

        $oXml = new DOMDocument('1.0', 'UTF-8');
        $oXml->loadXML($xmlResult);
        return new self($oXml);
    }

    public function parse()
    {
        $xPath = new DOMXPath($this->oXml);
        $sBasePlatformUrl = $xPath->query('/Data/baseImgUrl')->item(0)->nodeValue;
        $aGame = [];
        foreach ($xPath->query('/Data/Game/*') as $oNode) {
            ($oNode->nodeName === 'id' ? $aGame['id'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'GameTitle' ? $aGame['title'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'PlatformId' ? $aGame['idPlatform'] = $oNode->nodeValue : null);
            $oNode->nodeName === 'ReleaseDate'
                ? $aGame['releaseDate'] = VGL_Utils_ParseReleaseDate::parse($oNode->nodeValue)
                : null;
            ($oNode->nodeName === 'Overview' ? $aGame['overview'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'ESRB' ? $aGame['ESRB'] = $oNode->nodeValue : null);
            $oNode->nodeName === 'Genres' ? $aGame['Genres'] = self::parseGenres($oNode) : null;
            ($oNode->nodeName === 'Players' ? $aGame['players'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'Co-op' ? $aGame['isCoop'] = ($oNode->nodeValue === 'Yes') : null);
            ($oNode->nodeName === 'Youtube' ? $aGame['youtube'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'Publisher' ? $aGame['publisher'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'Developer' ? $aGame['developer'] = $oNode->nodeValue : null);
            ($oNode->nodeName === 'Rating' ? $aGame['rating'] = round($oNode->nodeValue, 6) : null);
            $oNode->nodeName === 'Images' ? $aGame['Images'] = self::parseImages($sBasePlatformUrl, $oNode) : null;
        }
        //Run html_entity_decode for all fields that is a string
        foreach ($aGame as &$value) {
            if (!is_string($value)) {
                continue;
            }
            $value = html_entity_decode($value, ENT_QUOTES);
        } unset($value);
        return $aGame;
    }

    public static function addCache($idGame)
    {
        if (false === self::_getCacheValue(self::SERVICE, $idGame)) {
            $aArgs = [];
            !empty($idGame) ? $aArgs['id'] = $idGame : null;
            $xmlResult = self::_callService(self::SERVICE, $aArgs);
            self::_setCacheValue($xmlResult, self::SERVICE, $idGame);
        }
    }

    public static function clearCache($idGame)
    {
        return self::_removeCacheValue(self::SERVICE, $idGame);
    }

}