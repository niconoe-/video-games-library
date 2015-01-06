<?php
class Model_Vgl_Platform extends VGL_Db_Table
{

    protected $_name = 'platform';
    protected $_primary = 'id';

    /**
     * Overload the parent save to manage pictures
     * @param array $aData
     * @return bool|int|mixed
     */
    public function save(array $aData = [])
    {
        if (empty($aData)) {
            $aData = $this->getData();
        }
        if (empty($aData)) {
            return parent::save($aData);
        }

        //Manage pictures in the key "Images" only if exists.
        if (!isset($aData['Images'])) {
            return parent::save($aData);
        }

        $aImages = $aData['Images'];
        unset($aData['Images']);

        if (empty($aImages)) {
            return parent::save($aData);
        }

        parent::save($aData);

        $oImage = new Model_Vgl_Image();
        foreach ($aImages as $aImage) {
            $idImage = $oImage->save($aImage);
            if ($idImage !== false) {
                $this->_db->insert('platforms_images', ['idPlatform' => $aData['id'], 'idImage' => $idImage]);
            }
        }

        return true;
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
}