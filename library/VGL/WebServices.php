<?php

/**
 * Class VGL_WebServices
 * @package VGL
 */
class VGL_WebServices
{

    const BASE_URL = 'http://thegamesdb.net/api/';

    /** @var DOMDocument  */
    public $oXml = null;

    final protected function __construct(DOMDocument $oXml)
    {
        $this->oXml = $oXml;
    }

    final public function __toString()
    {
        return $this->oXml->saveXML();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        $aXmlErrors = $this->oXml->getElementsByTagName('Error');
        $aErrors = [];
        foreach ($aXmlErrors as $aXmlError) {
            $aErrors[] = $aXmlError->nodeValue;
        }
        if (empty($aErrors)) {
            return false;
        }
        return $aErrors;
    }

    protected static function _getCacheValue()
    {
        $aArgs = func_get_args();
        if (empty($aArgs)) {
            return null;
        }
        $sCacheName = md5(implode('_', $aArgs));
        if (false === ($xmlResult = VGL_Cache::getCache()->load($sCacheName))) {
            return false;
        }

        return $xmlResult;
    }

    protected static function _setCacheValue()
    {
        $aArgs = func_get_args();
        if (empty($aArgs)) {
            return null;
        }

        $valueToStore = array_shift($aArgs);
        if (empty($aArgs)) {
            return null;
        }

        $sCacheName = md5(implode('_', $aArgs));
        VGL_Cache::save($valueToStore, $sCacheName);
        return $valueToStore;
    }

    protected static function _removeCacheValue()
    {
        $aArgs = func_get_args();
        if (empty($aArgs)) {
            return null;
        }
        $sCacheName = md5(implode('_', $aArgs));
        return VGL_Cache::getCache()->remove($sCacheName);
    }


    protected static function _callService($sMethod, $aArgs = [])
    {
        $sHttpQuery = (empty($aArgs) ? '' : ('?' . http_build_query($aArgs)));
        $sUrl = self::BASE_URL . $sMethod . '.php' . $sHttpQuery;
        $sContent = file_get_contents($sUrl);
        return $sContent;
    }

    public function parseGenres(DOMNode $oNode)
    {
        if (!$oNode->hasChildNodes()) {
            return [];
        }

        $aGenres = [];
        /** @var DOMNode $oChild */
        foreach ($oNode->childNodes as $oChild) {
            if ($oChild->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            $sNodeName = strtolower($oChild->nodeName);
            if ($sNodeName === 'genre') {
                $aGenres[] = $oChild->nodeValue;
            }
        }
        return $aGenres;
    }


    public function parseImages($sBaseUrl, DOMNode $oNode)
    {
        if (!$oNode->hasChildNodes()) {
            return [];
        }

        $aImages = [];
        /** @var DOMNode $oChild */
        foreach ($oNode->childNodes as $oChild) {
            if ($oChild->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            $sNodeName = strtolower($oChild->nodeName);
            if ($sNodeName === 'fanart') {
                $aImages = array_merge($aImages, $this->_parseImageFanart($sBaseUrl, $oChild));
            } elseif ($sNodeName === 'boxart') {
                $aImages = array_merge($aImages, $this->_parseImageBoxart($sBaseUrl, $oChild));
            } elseif ($sNodeName === 'banner') {
                $aImages = array_merge($aImages, $this->_parseImageBanner($sBaseUrl, $oChild));
            } elseif ($sNodeName === 'clearlogo') {
                $aImages = array_merge($aImages, $this->_parseImageLogo($sBaseUrl, $oChild));
            } elseif ($sNodeName === 'screenshot') {
                $aImages = array_merge($aImages, $this->_parseImageScreenshot($sBaseUrl, $oChild));
            } elseif ($sNodeName === 'consoleart') {
                $aImages = array_merge($aImages, $this->_parseImageConsoleart($sBaseUrl, $oChild));
            } elseif ($sNodeName === 'controllerart') {
                $aImages = array_merge($aImages, $this->_parseImageControllerart($sBaseUrl, $oChild));
            }
        }
        return $aImages;
    }

    private function _parseImageFanart($sBaseUrl, DOMNode $oNode)
    {
        /** @var DOMNode $oChild */
        foreach ($oNode->childNodes as $oChild) {
            if ($oChild->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            $sNodeName = strtolower($oChild->nodeName);
            if ($sNodeName === 'original') {
                $oAttr = $oChild->attributes;
                $aOriginal = [
                    'category' => 'fanart',
                    'scaleType' => 'original',
                    'width' => $oAttr->getNamedItem('width')->nodeValue,
                    'height' => $oAttr->getNamedItem('height')->nodeValue,
                    'relativeURL' => $sBaseUrl . $oChild->nodeValue,
                ];
            } elseif ($sNodeName === 'thumb') {
                $aThumb = [
                    'category' => 'fanart',
                    'scaleType' => 'thumb',
                    'relativeURL' => $sBaseUrl . $oChild->nodeValue,
                ];
            }
        }

        $aReturn = [];
        if (isset($aOriginal)) {
            $aReturn[] = $aOriginal;
        }
        if (isset($aThumb)) {
            $aReturn[] = $aThumb;
        }

        return $aReturn;
    }

    private function _parseImageBoxart($sBaseUrl, DOMNode $oNode)
    {
        $oAttr = $oNode->attributes;
        $aOriginal = [
            'category' => 'boxart' . ucfirst($oAttr->getNamedItem('side')->nodeValue),
            'scaleType' => 'original',
            'width' => $oAttr->getNamedItem('width')->nodeValue,
            'height' => $oAttr->getNamedItem('height')->nodeValue,
            'relativeURL' => $sBaseUrl . $oNode->nodeValue,
        ];

        if (!is_null($oAttr->getNamedItem('thumb')) && trim($oAttr->getNamedItem('thumb')->nodeValue) != '') {
            $aThumb = [
                'category' => 'boxart' . ucfirst($oAttr->getNamedItem('side')->nodeValue),
                'scaleType' => 'thumb',
                'relativeURL' => $sBaseUrl . $oAttr->getNamedItem('thumb')->nodeValue,
            ];
            return [$aOriginal, $aThumb];
        }
        return [$aOriginal];
    }

    private function _parseImageBanner($sBaseUrl, DOMNode $oNode)
    {
        return [
            [
                'category' => 'banner',
                'scaleType' => 'original',
                'relativeURL' => $sBaseUrl . $oNode->nodeValue,
                'width' => $oNode->attributes->getNamedItem('width')->nodeValue,
                'height' => $oNode->attributes->getNamedItem('height')->nodeValue,
            ]
        ];
    }

    private function _parseImageLogo($sBaseUrl, DOMNode $oNode)
    {
        return [
            [
                'category' => 'logo',
                'scaleType' => 'original',
                'relativeURL' => $sBaseUrl . $oNode->nodeValue,
                'width' => $oNode->attributes->getNamedItem('width')->nodeValue,
                'height' => $oNode->attributes->getNamedItem('height')->nodeValue,
            ]
        ];
    }

    private function _parseImageScreenshot($sBaseUrl, DOMNode $oNode)
    {
        /** @var DOMNode $oChild */
        foreach ($oNode->childNodes as $oChild) {
            if ($oChild->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }
            $sNodeName = strtolower($oChild->nodeName);
            if ($sNodeName === 'original') {
                $oAttr = $oChild->attributes;
                $aOriginal = [
                    'category' => 'screenshot',
                    'scaleType' => 'original',
                    'width' => $oAttr->getNamedItem('width')->nodeValue,
                    'height' => $oAttr->getNamedItem('height')->nodeValue,
                    'relativeURL' => $sBaseUrl . $oChild->nodeValue,
                ];
            } elseif ($sNodeName === 'thumb') {
                $aThumb = [
                    'category' => 'screenshot',
                    'scaleType' => 'thumb',
                    'relativeURL' => $sBaseUrl . $oChild->nodeValue,
                ];
            }
        }

        $aReturn = [];
        if (isset($aOriginal)) {
            $aReturn[] = $aOriginal;
        }
        if (isset($aThumb)) {
            $aReturn[] = $aThumb;
        }

        return $aReturn;
    }

    private function _parseImageConsoleart($sBaseUrl, DOMNode $oNode)
    {
        return [
            [
                'category' => 'consoleArt',
                'scaleType' => 'original',
                'relativeURL' => $sBaseUrl . $oNode->nodeValue,
            ]
        ];
    }

    private function _parseImageControllerart($sBaseUrl, DOMNode $oNode)
    {
        return [
            [
                'category' => 'controllerArt',
                'scaleType' => 'original',
                'relativeURL' => $sBaseUrl . $oNode->nodeValue,
            ]
        ];
    }
}