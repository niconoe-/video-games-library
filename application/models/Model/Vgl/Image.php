<?php
class Model_Vgl_Image extends VGL_Db_Table
{

    protected $_name = 'image';
    protected $_primary = 'id';
    protected $_sequence = true;


    public function save(array $aData = [])
    {
        if (empty($aData)) {
            $aData = $this->getData();
        }
        if (empty($aData)) {
            return parent::save($aData);
        }

        $sUrl = $aData['relativeURL'];
        if (!$this->existsRelativeURL($sUrl)) {
            //Insert
            $id = $this->insert($aData);
            return $id;
        }
        return false;
    }


    public function existsRelativeURL($sURL)
    {
        $oResult = $this->fetchRow($this->select()->where('relativeURL = ?', $sURL));
        if (is_null($oResult)) {
            return false;
        }
        return true;
    }
}