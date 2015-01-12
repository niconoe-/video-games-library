<?php
class Model_Vgl_Genre extends VGL_Db_Table
{

    protected $_name = 'genre';
    protected $_primary = 'id';
    protected $_sequence = true;

    /**
     * Overload the parent save to manage genres
     * @param array $aData
     * @return bool|int|mixed
     */
    public function save(array $aData = [])
    {
        if (empty($aData)) {
            $aData = $this->getData();
        }

        $aResult = $this->fetchRow($this->select()->where('genre = ?', $aData['genre']));
        if (!is_null($aResult)) {
            return $aResult['id'];
        }

        return parent::save($aData);
    }


    /**
     * Save one genre by its name
     * @param string $sGenre
     * @return bool|int|mixed
     */
    public function saveOne($sGenre)
    {
        return $this->save(['genre' => $sGenre]);
    }

}