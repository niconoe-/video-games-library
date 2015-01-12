<?php

/**
 * Class VGL_WebServices_GamesList
 * @package VGL_WebServices
 */
class VGL_WebServices_GamesList extends VGL_WebServices implements VGL_WebServices_Interface
{
    const SERVICE = 'GetGamesList';

    /**
     * @param string ...$name[, $platform[, $genre]]
     * @return VGL_WebServices_GamesList
     */
    public static function get()
    {
        $aArgs = func_get_args();
        if (empty($aArgs)) {
            throw new BadMethodCallException('Argument 1 (name) is missing.');
        }

        $name = array_shift($aArgs);
        $platform = '';
        $genre = '';
        if (!empty($aArgs)) {
            $platform = array_shift($aArgs);
        }
        if (!empty($aArgs)) {
            $genre = array_shift($aArgs);
        }

        $xmlResult = self::_getCacheValue(self::SERVICE, $name, $platform, $genre);
        if ($xmlResult === null) {
            return null;
        }
        if ($xmlResult === false) {
            $aArgs = [];
            !empty($name)     ? $aArgs['name']      = $name     : null;
            !empty($platform) ? $aArgs['platform']  = $platform : null;
            !empty($genre)    ? $aArgs['genre']     = $genre    : null;
            $xmlResult = self::_callService(self::SERVICE, $aArgs);
            $xmlResult = self::_setCacheValue($xmlResult, self::SERVICE, $name, $platform, $genre);
        }

        $oXml = new DOMDocument('1.0', 'UTF-8');
        $oXml->loadXML($xmlResult);
        return new self($oXml);
    }

    public function parse()
    {
        $aXmlGames = $this->oXml->getElementsByTagName('Game');
        $aGames = [];
        foreach ($aXmlGames as $oXmlGame) {
            $id = $oXmlGame->getElementsByTagName('id')->item(0);
            $gameTitle = $oXmlGame->getElementsByTagName('GameTitle')->item(0);
            $releaseDate = $oXmlGame->getElementsByTagName('ReleaseDate')->item(0);
            $platform = $oXmlGame->getElementsByTagName('Platform')->item(0);
            if ($releaseDate !== null) {
                $releaseDate = VGL_Utils_ParseReleaseDate::parse($releaseDate->nodeValue);
            }
            $aGames[] = [
                'id' => $id->nodeValue,
                'GameTitle' => $gameTitle->nodeValue,
                'ReleaseDate' => $releaseDate,
                'Platform' => ($platform === null ? null : $platform->nodeValue),
            ];
        }
        return $aGames;
    }

}