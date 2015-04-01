<?php
/**
 * Model klasy ustawien klienta
 * @author Mariusz Filipkowski
 *
 */
class Application_Model_DbTable_Settings extends Zend_Db_Table_Abstract
{

	protected $_name = 'settings';

	public function addSettings(array $data)
	{
		$page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
		return $this->insert($data);
	}

	public function updateSettings($id, array $data)
	{	
		$page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
		return $this->update($data, 'id = '. (int)$id);
	}
	public function checkExist()
	{	
		$page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
		$row = $this->fetchRow('id=',1);
		if (!$row) {
			return false;
		}else{
			
			return true;
		}

	}



	public function getSettings()
	{
		
		$page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
		if(!isset($user->id_client))
			return false;
			
		$row = $this->fetchRow('id = ' . 1);
		if (!$row) {
			return false;
		}else{
			return $row->toArray();	
		}
		
	}
	public function getSettingsByClient($id_client)
	{
			
		$row = $this->fetchRow('id_client = ' . $id_client);
		if (!$row) {
			return false;
		}else{
			return $row->toArray();	
		}
		
	}


	public function getSingleWithLabel($name)
	{
		$page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
		$select = $this->select();
		$where = $this->getAdapter()->quoteInto('label = ?', $name);
		$select->where($where)->where('id_client=?',$user->id_client);
		return $this->fetchRow($select);
	}

	public function fetchAllSettingsfield($options) {
		$select = $this->select()->setIntegrityCheck(false);
		$select->distinct();
		$select->from("additional_field");
		if(isset($options['field'])) {
			$select->order($options['field'].' '.$options['dir']);
		}

		$rows = $this->fetchAll($select);

		return $rows->toArray();
	}

}

?>
