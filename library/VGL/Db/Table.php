<?php

class Vgl_Db_Table extends Zend_Db_Table
{
    protected $_data = [];
    protected $_aErrors = [];

	public function __get($key)
	{
		return (isset($this->_data[$key])) ? $this->_data[$key] : null;
	}

	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
		return $this;
	}

    public function __isset($key)
    {
        return (isset($this->_data[$key]));
    }

    public function __unset($key)
    {
        if (isset($this->_data[$key])) {
            unset($this->_data[$key]);
        }
    }

    public function getData()
    {
    	return $this->_data;
    }

    public function setData($aData)
    {
        $this->_data = $aData;
        return $this;
    }

    public function init()
    {
        $this->_data = [];
        $this->_aErrors = [];
        return $this;
    }

    public function hasError()
    {
        return (!empty($this->_aErrors));
    }

    public function addError($error)
    {
        $this->_aErrors[] = $error;
        return $this;
    }

    public function getErrors()
    {
        return $this->_aErrors;
    }

	public function feed($primaryParam)
	{
		$oSelect = null;
		if (is_string($this->_primary) && !is_array($primaryParam)) {
			// $this->select() override $this->_primary to set an array instead of string
			$oSelect = $this->select()->where(reset($this->_primary) . ' = ?', $primaryParam);
		} elseif (is_array($this->_primary) && is_array($primaryParam)
				  && count($this->_primary) === count($primaryParam)
		) {
			$oSelect = $this->select();
			foreach ($this->_primary as $iIndex => $sColname) {
				if (isset($primaryParam[$sColname])) {
					$oSelect->where($sColname . ' = ?', $primaryParam[$sColname]);
				} elseif (isset($primaryParam[$iIndex])) {
					$oSelect->where($sColname . ' = ?', $primaryParam[$iIndex]);
				}
			}
		}
		if (($oSelect !== null) && ($row = $this->fetchRow($oSelect))) {
            $this->_data = $row->toArray();
            return true;
		}
		return false;
	}

    public function save(array $aData = [])
    {
        if (!empty($aData)) {
            $this->setData($aData);
        }

        if (empty($this->_data)) {
            $this->addError('Try to insert or update no data.');
            return false;
        }
        try {
            $aPrimary = (!is_array($this->_primary)) ? [$this->_primary] : $this->_primary;
            $aPrimaryWhere = [];
            foreach ($aPrimary as $sCol) {
                // If full or part of primary key is missing in the data, insert
                if (!isset($this->$sCol) || is_null($this->$sCol)) {
                    return $this->insert($this->_data);
                }
                $aPrimaryWhere[$sCol . ' = ?'] = $this->$sCol;
            }
            //Now, we know the where condition: if value exists for this, update. If not, insert.
            $oSql = $this->select();
            foreach ($aPrimaryWhere as $cond => $value) {
                $oSql->where($cond, $value);
            }
            if ($this->fetchRow($oSql)) {
                return $this->update($this->_data, $aPrimaryWhere);
            }
            return $this->insert($this->_data);
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            return false;
        }
    }

}