<?php

/**
 * Class IndexController
 */
class IndexController extends VGL_Controller_Action
{

    public function indexAction()
    {
        $this->view->activeNavTab = 'Rechercher';
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

        $this->view->platformList = $this->_getSortPlatforms();
    }

    public function rechercherAction()
    {
        if (!$this->getRequest()->isPost() || (null == ($sQueryString = $this->_getParam('search')))) {
            $this->redirect('/');
            return;
        }

        // action body
        $this->view->activeNavTab = 'Rechercher';
        $sQueryString = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $sQueryString);
        $sQueryString = strtolower(trim($sQueryString));

        if (strlen($sQueryString) < 2) {
            $oMessage = VGL_Utils_CreateMessage::danger(
                'Votre recherche doit contenir au minimum 3 caractères alphanumériques.'
            );
            $this->_helper->FlashMessenger($oMessage);
            $this->redirect('/');
            return;
        }

        $sPlatform = $this->_getParam('platform');

        $oXml = VGL_WebServices_GamesList::get($sQueryString, $sPlatform);
        if (false !== ($aErrors = $oXml->getErrors())) {
            $sMessage = implode('.<br>', $aErrors);
            $sMessage = '<b>Error from thegamesdb.net API:</b> ' . $sMessage . '.';
            $oMessage = VGL_Utils_CreateMessage::danger($sMessage);
            $this->_helper->FlashMessenger($oMessage);
            $this->redirect('/');
            return;
        }

        $aGames = $oXml->parse();
        if (empty($aGames)) {
            $this->_helper->FlashMessenger(VGL_Utils_CreateMessage::warning('Aucun résultat trouvé.'));
            $this->redirect('/');
            return;
        }

        $aGameList = [];
        foreach ($aGames as $aGame) {
            $aGameList[] = [
                'label' => '<b>' . $aGame['GameTitle'] . '</b> sur ' . $aGame['Platform']
                    . (null === $aGame['ReleaseDate'] ? '' : (' le ' . $aGame['ReleaseDate'])),
                'id' => $aGame['id']
            ];
        }

        $this->view->aGameList = $aGames;
        $this->view->sSearch = $sQueryString;
        $this->view->platformList = $this->_getSortPlatforms();
        $this->view->sPlatform = $sPlatform;
    }


    private function _getSortPlatforms()
    {
        $oPlatform = new Model_Vgl_Platform();
        $aList = $oPlatform->getSearchDropdownList();
        return $aList;
    }

}

