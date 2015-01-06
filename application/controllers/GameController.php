<?php

/**
 * Class GameController
 */
class GameController extends VGL_Controller_Action
{

    public function indexAction()
    {
        $this->view->activeNavTab = 'Mes jeux';
        $this->view->flashMessage = '';
        $aFlashMessages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        if (!empty($aFlashMessages)) {
            $sAlertFlashMessage = '';
            foreach (VGL_Utils_CreateMessage::getMessagesByType($aFlashMessages) as $type => $aMessages) {
                $sMessages = '<p class="text-center">' . implode(', ', $aMessages) . '</p>';
                $sAlertFlashMessage .= $this->view->alert($sMessages)->$type();
            }
            $this->view->flashMessage = $sAlertFlashMessage;
        }
    }

    public function infoAction()
    {
        $idGame = $this->_getParam('id');
        if (is_null($idGame)) {
            $this->_helper->FlashMessenger(VGL_Utils_CreateMessage::danger('Accès à un jeu inexistant.'));
            $this->redirect('/');
            return;
        }

        $oXml = VGL_WebServices_Game::get($idGame);
        if (false !== ($aErrors = $oXml->getErrors())) {
            $sMessage = implode('.<br>', $aErrors);
            $sMessage = '<b>Error from thegamesdb.net API:</b> ' . $sMessage . '.';
            $oMessage = VGL_Utils_CreateMessage::danger($sMessage);
            $this->_helper->FlashMessenger($oMessage);
            $this->redirect('/');
            return;
        }

        $aGame = $oXml->parse();
        if (empty($aGame)) {
            $this->_helper->FlashMessenger(VGL_Utils_CreateMessage::danger('Accès à un jeu inexistant.'));
            $this->redirect('/');
            return;
        }

        if (!empty($aGame['overview'])) {
            $this->view->headScript()->appendFile('/js/jquery.ellipsis-1.1.1-fork-niconoe.min.js');
        }

        $this->view->aGame = $aGame;
        $this->view->aBanners = $this->_getBanners($aGame);
        $this->view->aAsidePics = array_merge($this->_getScreenshots($aGame), $this->_getFanarts($aGame));
        $this->view->aBoxarts = $this->_getBoxarts($aGame);

        if (!empty($aGame['idPlatform'])) {
            $oPlatform = new Model_Vgl_Platform();
            if ($oPlatform->feed($aGame['idPlatform'])) {
                $this->view->oPlatform = $oPlatform;
            }
        }


    }

    public function addAction()
    {

    }

    private function _sortPictures($aGame)
    {
        if (empty($aGame['Images'])) {
            return false;
        }
        $aPicturesByCategory = [];
        foreach ($aGame['Images'] as $aPicture) {
            $aPicturesByCategory[$aPicture['category']][] = $aPicture;
        }

        return $aPicturesByCategory;

    }


    private function _getBanners($aGame)
    {
        $aPictures = $this->_sortPictures($aGame);
        return (!empty($aPictures['banner']) ? $aPictures['banner'] : []);
    }

    private function _getScreenshots($aGame)
    {
        $aPictures = $this->_sortPictures($aGame);
        return (!empty($aPictures['screenshot']) ? $aPictures['screenshot'] : []);
    }

    private function _getFanarts($aGame)
    {
        $aPictures = $this->_sortPictures($aGame);
        return (!empty($aPictures['fanart']) ? $aPictures['fanart'] : []);
    }

    private function _getBoxarts($aGame)
    {
        $aPictures = $this->_sortPictures($aGame);
        return (
            (!empty($aPictures['boxartFront']) && !empty($aPictures['boxartBack']))
                ? array_merge($aPictures['boxartFront'], $aPictures['boxartBack'])
                : []
        );
    }

}

