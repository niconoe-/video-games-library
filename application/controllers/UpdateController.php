<?php

/**
 * Class UpdateController
 */
class UpdateController extends VGL_Controller_Action
{
    /** @var Model_Vgl_Update $oUpdateModel */
    public $oUpdateModel = null;

    public function init()
    {
        $this->oUpdateModel = new Model_Vgl_Update();
        set_time_limit(0);
        parent::init();
    }


    public function indexAction()
    {
        $this->view->activeNavTab = 'Update';
        $this->view->flashMessage = '';
        $this->view->flashMessageUpdateGameList = '';
        $this->view->flashMessageUpdatePlatformList = '';
        $this->view->addSectionClearCache = false;
        $this->view->dateLastUpdateGameList = false;
        $this->view->dateLastUpdatePlatformList = false;

        $aFlashMessages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        if (!empty($aFlashMessages)) {
            $sAlertFlashMessage = '';
            foreach (VGL_Utils_CreateMessage::getMessagesByType($aFlashMessages) as $type => $aMessages) {
                $sMessages = '<p class="text-center">' . implode(', ', $aMessages) . '</p>';
                $sAlertFlashMessage .= $this->view->alert($sMessages)->$type();
            }

            $oSession = new Zend_Session_Namespace('FromPreviousUpdateAction');
            $sPrevious = $oSession->previous;

            if ($sPrevious === 'UpdateGameList') {
                $this->view->flashMessageUpdateGameList = $sAlertFlashMessage;
            } elseif ($sPrevious === 'UpdatePlatformList') {
                $this->view->flashMessageUpdatePlatformList = $sAlertFlashMessage;
            } else {
                $this->view->flashMessage = $sAlertFlashMessage;
            }
        }

        //Check existence of cache data
        if (count(VGL_Cache::getCache()->getIds()) > 1) {
            $this->view->addSectionClearCache = true;
        }

        //Get lest dates for updates
        $lastUpdate = $this->oUpdateModel->getLast('Game');
        $lastUpdate = strtotime($lastUpdate);
        $this->view->dateLastUpdateGameList = ($lastUpdate != false) ? date('d/m/Y à H:i:s', $lastUpdate) : false;
        $lastUpdate = $this->oUpdateModel->getLast('Platform');
        $lastUpdate = strtotime($lastUpdate);
        $this->view->dateLastUpdatePlatformList = ($lastUpdate != false) ? date('d/m/Y à H:i:s', $lastUpdate) : false;
    }

    public function gameListAction()
    {
        $sUrlBack = '/update/index';
        $lastUpdate = strtotime($this->oUpdateModel->getLast('Game'));
        if ($lastUpdate == false) {
            $lastUpdate = 30*24*60*60; //30 days
        } else {
            $lastUpdate = time() - $lastUpdate;
        }

        $oXml = VGL_WebServices_Update::get($lastUpdate);
        if (false !== ($aErrors = $oXml->getErrors())) {
            $sMessage = implode('.<br>', $aErrors);
            $sMessage = '<b>Error from thegamesdb.net API:</b> ' . $sMessage . '.';
            $oMessage = VGL_Utils_CreateMessage::danger($sMessage);
            $this->_helper->FlashMessenger($oMessage);
            $this->redirect($sUrlBack);
            return;
        }

        $aItems = $oXml->parse();
        foreach ($aItems as $sIdGame) {
            VGL_WebServices_Game::clearCache($sIdGame);
            VGL_WebServices_Game::addCache($sIdGame);
        }

        if (empty($aItems)) {
            $sMessage = 'No updated games.';
            $oMessage = VGL_Utils_CreateMessage::warning($sMessage);
            $this->_helper->FlashMessenger($oMessage);
        } else {
            $sMessage = count($aItems) . ' updated games.';
            $oMessage = VGL_Utils_CreateMessage::success($sMessage);
            $this->_helper->FlashMessenger($oMessage);
        }

        $oSession = new Zend_Session_Namespace('FromPreviousUpdateAction');
        $oSession->setExpirationHops(1, null, true);
        $oSession->previous = 'UpdateGameList';

        //Update the last update date
        $this->oUpdateModel->insertNow('Game');
        $this->redirect($sUrlBack);
    }

    public function platformListAction()
    {
        $sUrlBack = '/update/index';

        $oXml = VGL_WebServices_PlatformsList::get();
        if (false !== ($aErrors = $oXml->getErrors())) {
            $sMessage = implode('.<br>', $aErrors);
            $sMessage = '<b>Error from thegamesdb.net API:</b> ' . $sMessage . '.';
            $oMessage = VGL_Utils_CreateMessage::danger($sMessage);
            $this->_helper->FlashMessenger($oMessage);
            $this->redirect($sUrlBack);
            return;
        }

        $aPlatforms = $oXml->parse();
        if (empty($aPlatforms)) {
            $this->_helper->FlashMessenger(VGL_Utils_CreateMessage::warning('No platform in list.'));
            $this->redirect($sUrlBack);
            return;
        }

        $oPlatform = new Model_Vgl_Platform();
        $nbMajPlatform = 0;
        $aErrors = [];
        foreach ($aPlatforms as $idPlatform) {
            $oXml = VGL_WebServices_Platform::get($idPlatform);
            if (false === $oXml->isPlatform()) {
                $sMessage = 'Unknown id of platform "' . $idPlatform . '".';
                $oMessage = VGL_Utils_CreateMessage::danger($sMessage);
                $this->_helper->FlashMessenger($oMessage);
                continue;
            }
            $aInfoPlatform = $oXml->parse();

            $oPlatform->save($aInfoPlatform);
            if ($oPlatform->hasError()) {
                $msg = 'Error for platform #' . $aInfoPlatform['id'] . ': ' . implode('. ', $oPlatform->getErrors());
                $aErrors[] = $msg;
            } else {
                $nbMajPlatform++;
            }
            $oPlatform->init();

        }

        if (!empty($aErrors)) {
            $sMessage = implode('.<br>', $aErrors);
            $oMessage = VGL_Utils_CreateMessage::danger($sMessage);
            $this->_helper->FlashMessenger($oMessage);
        }
        if ($nbMajPlatform > 0) {
            $sMessage = $nbMajPlatform . ' updated platforms.';
            $oMessage = VGL_Utils_CreateMessage::success($sMessage);
            $this->_helper->FlashMessenger($oMessage);
        } else {
            $sMessage = 'No updated platforms.';
            $oMessage = VGL_Utils_CreateMessage::warning($sMessage);
            $this->_helper->FlashMessenger($oMessage);
        }

        $oSession = new Zend_Session_Namespace('FromPreviousUpdateAction');
        $oSession->setExpirationHops(1, null, true);
        $oSession->previous = 'UpdatePlatformList';

        //Update the last update date if no errors
        if (empty($aErrors)) {
            $this->oUpdateModel->insertNow('Platform');
        }

        $this->redirect($sUrlBack);
    }

    public function cleanCacheAction()
    {
        try {
            VGL_Cache::getCache()->clean();
            $sMsg = 'Le cache a bien été vidé.';
            $this->_helper->getHelper('FlashMessenger')->addMessage(VGL_Utils_CreateMessage::success($sMsg));
            $this->redirect('/update/index');
            return;
        } catch (Zend_Cache_Exception $e) {
            $sMsg = 'Error from cache: ' . $e->getMessage();
        } catch (Exception $e) {
            $sMsg = 'Error: ' . $e->getMessage();
        }
        $this->_helper->getHelper('FlashMessenger')->addMessage(VGL_Utils_CreateMessage::danger($sMsg));
        $this->redirect('/update/index');
        return;
    }
}

