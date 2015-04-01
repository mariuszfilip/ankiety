<?php
abstract class Application_Model_DbTable_DbModel 
	extends Zend_Db_Table_Abstract
	implements Application_Model_Api_ApiInterface
{
		
	/**
	 * (non-PHPdoc)
	 * @see Application_Model_Api_ApiInterface::apiFetchAll()
	 */
	public function apiFetch(array $params, $id_client) {
		
		$select = $this->select()->setIntegrityCheck(false);
		
		if ($params['fields']) {
			$select->from($this->_name, array_merge(array('id'), $params['fields']));
		} elseif ($this->hasField('name')) {
			$select->from($this->_name, array('id', 'name'));
		} else {
			$select->from($this->_name, array('id'));
		}
		
		if ($this->hasField('id_client')) {
			$select->where("id_client = ?", $id_client);
		}
		
		if ($params['from'] && $params['limit']) {
			$select->limit($params['limit'], $params['from']);
		}
		 
		$rows = $this->fetchAll($select);
		return $rows->toArray();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Application_Model_Api_ApiInterface::apiGet()
	 */
	public function apiGet(array $params, $id_client) {
		$result = $this->find($params['id'])->toArray();
		if (($this->hasField('id_client') && $result[0]['id_client'] == $id_client) || (!$this->hasField('id_client'))) {
			return $result[0];
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Application_Model_Api_ApiInterface::apiAdd()
	 */
	public function apiAdd(array $params, $id_client) {
		if ($this->hasField('id_client')) { 
			$params['id_client'] = $id_client;
		}
		return array("id" => $this->insert($params));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Application_Model_Api_ApiInterface::apiUpdate()
	 */
	public function apiUpdate(array $params, $id_client) {
		$result = $this->find($params['id'])->toArray();
		$object = $result[0];
		if (($this->hasField('id_client') && $object['id_client'] == $id_client) || (!$this->hasField('id_client'))) {
			$data = $params;
			unset($data['id']);
			return array("success" => $this->update($data, "id = " . $params['id']));
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Application_Model_Api_ApiInterface::apiRemove()
	 */
	public function apiRemove(array $params, $id_client) {
		$result = $this->find($params['id'])->toArray();
		$object = $result[0];
		if (($this->hasField('id_client') && $object['id_client'] == $id_client) || (!$this->hasField('id_client'))) {

			if($this->hasField('deleted')) {
				$data = array('deleted' => 1);
				return array("success" => $this->update($data, "id = " . $params['id']));
			} else {
				return array("success" => $this->delete("id = ".$params['id']));
			}
			
		}
		return false;
	}
	

	/**
	 * Sprawdza czy tabela ma pole o danej nazwie (np. "name")
	 */
	protected function hasField($fieldName) {
		$columns = $this->getAdapter()->describeTable($this->_name);
		if (array_key_exists($fieldName, $columns)) {
			return true;
		}
		return false;
	}
		
}