<?php
class Model_Vgl_Update extends VGL_Db_Table
{

    protected $_name = 'update';
    protected $_primary = 'id';
    protected $_sequence = true;


    public function insertNow($sType)
    {
        $aData = [
            'timestampAtRun' => new Zend_Db_Expr('NOW()'),
            'type' => $sType,
        ];
        return $this->save($aData);
    }

    public function getLast($sType)
    {
        $oSql = $this->_db->select()->from($this->_name, ['MAX(timestampAtRun)'])->where('type = ?', $sType);
        return $this->_db->fetchOne($oSql);
    }

}