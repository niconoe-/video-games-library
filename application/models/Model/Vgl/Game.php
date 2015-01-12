<?php
class Model_Vgl_Game extends VGL_Db_Table
{

    protected $_name = 'game';
    protected $_primary = 'id';

    /**
     * Overload the parent save to manage pictures and genres
     * @param array $aData
     * @return bool|int|mixed
     */
    public function save(array $aData = [])
    {
        if (empty($aData)) {
            $aData = $this->getData();
        }

        $aData['addedDate'] = date('Y-m-d H:i:s');

        try {
            $this->_db->beginTransaction();

            //Manage pictures in the key "Images" only if exists.
            if (!empty($aData['Images'])) {
                $aImages = $aData['Images'];
                unset($aData['Images']);

                $oImage = new Model_Vgl_Image();
                foreach ($aImages as $aImage) {
                    $idImage = $oImage->save($aImage);
                    if ($idImage !== false) {
                        $this->_db->insert('games_images', ['idGame' => $aData['id'], 'idImage' => $idImage]);
                    }
                }
            }

            //Manage genres in the key "Genres" only if exists.
            if (!empty($aData['Genres'])) {
                $aGenres = $aData['Genres'];
                unset($aData['Genres']);

                $oGenre = new Model_Vgl_Genre();
                foreach ($aGenres as $sGenre) {
                    $idGenre = $oGenre->saveOne($sGenre);
                    if ($idGenre !== false) {
                        $this->_db->insert('games_genres', ['idGame' => $aData['id'], 'idGenre' => $idGenre]);
                    }
                }
            }

            if (!empty($aData['releaseDate'])) {
                $aData['releaseDate'] = VGL_Utils_ParseReleaseDate::fromViewtoSql($aData['releaseDate']);
            }

            parent::save($aData);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            return $e->getMessage();
        }
    }

    public function getSearchDropdownList()
    {
        $aDevList = ['Nintendo', 'Sega', 'Sony', 'Microsoft'];
        $sql = $this->_db->select()->from($this->_name, ['name', 'developer'])->where('developer IN (?)', $aDevList);
        $aList = $this->_db->fetchAll($sql);
        $aGrouppedList = [];
        foreach ($aList as $aPlatform) {
            isset($aGrouppedList[$aPlatform['developer']])
                ? $aGrouppedList[$aPlatform['developer']][] = $aPlatform['name']
                : $aGrouppedList[$aPlatform['developer']] = [$aPlatform['name']];
        }

        //Only keep groups that have more than 2 elements in
        $aFilterredGrouppedList = [];
        foreach ($aGrouppedList as $sDev => $aListPlatform) {
            sort($aListPlatform);
            $aFilterredGrouppedList[$sDev] = $aListPlatform;
        }

        ksort($aFilterredGrouppedList);
        return $aFilterredGrouppedList;
    }

    public function getOwnedGames()
    {
        $aGames = $this->_db->fetchAll($this->_db->select()->from($this->_name, ['id', 'addedDate']));
        if (PHP_VERSION_ID < 50500) {
            $aReturn = [];
            foreach ($aGames as $aGame) {
                $aReturn[$aGame['id']] = $aGame['addedDate'];
            }
            return $aReturn;
        } else {
            return array_column($aGames, 'addedDate', 'id');
        }
    }
}