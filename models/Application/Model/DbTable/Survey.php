<?php
/**
 * Model klasy ankiet
 * @author Mariusz Filipkowski
 *
 */

class Application_Model_DbTable_Survey extends Zend_Db_Table_Abstract
{

	protected $_name = 'survey';

	public function addSurvey(array $data)
	{
		return $this->insert($data);
	}

	public function updateSurvey($id, array $data)
	{
		return $this->update($data, 'id = '. (int)$id);
	}

	public function deleteSurvey($id)
	{
		$this->delete('id =' . (int)$id);
	}
	public function getSurvey($id){
	    $page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
	    $select = $this->select();
	    $select->where('id=?',(int)$id);
	    $row = $this->fetchRow($select);
	    if($row instanceof Zend_Db_Table_Row){
	        return $row->toArray();
	    }
	    return false;
	    
	}
    public function getListSurvey(){
	    $page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
	    $select = $this->select();
	    $rows = $this->fetchAll($select);
	    if($rows instanceof Zend_Db_Table_Rowset){
	        return $rows->toArray();
	    }
	    return false;
	    
	}
	public function checkExist($name){
	 	$page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
	    $select = $this->select();
	    $select->where('name=?',$name);
	    $row = $this->fetchRow($select);
	    if($row instanceof Zend_Db_Table_Row){
	        return $row->toArray();
	    }
	    return false;   
	    
	}
    public function getByHash($hash){
	    $select = $this->select();
	    $select->where('hash=?',$hash);
	    $row = $this->fetchRow($select);
	    if($row instanceof Zend_Db_Table_Row){
	        return $row->toArray();
	    }
	    return false;       
	    
	    
	}
}

?>
