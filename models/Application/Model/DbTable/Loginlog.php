<?php
/**
 * Model historii logowań
 * @author Mariusz Filipkowski
 *
 */

class Application_Model_DbTable_Loginlog extends Zend_Db_Table_Abstract
{

	protected $_name = 'login_log';

	public function addLog(array $data)
	{
		return $this->insert($data);
	}
	public function updateLog($id, array $data)
	{
		return $this->update($data, 'id = '. (int)$id);
	}
	public function getDateLastCorrectLogin(){
	   $page_session_space = new Zend_Session_Namespace('page');
       $user=$page_session_space->user;
	   $select=$this->select();
	   $select->where('status=?',1);
	   $select->where('email=?',$user->email);
	   $select->order('date desc');
	   $select->limit(2,1);
	   $row =$this->fetchRow($select);
	   if($row){
	      $row=$row->toArray();
	      return $row['date'];
	   }else{
	      return false;
	    
	   }
	 
	}
    public function getDateLastFailedLogin(){
       $page_session_space = new Zend_Session_Namespace('page');
       $user=$page_session_space->user;
	   $select=$this->select();
	   $select->where('status=?',0);
	   $select->where('email=?',$user->email);
	   $select->order('date desc');
	   $select->limit(1);
	   $row =$this->fetchRow($select);
	   if($row){
	      $row=$row->toArray();
	      return $row['date'];
	   }else{
	      return false;
	    
	   }
	   
	}
	public function fetchAllLog($options) {
	    $page_session_space = new Zend_Session_Namespace('page');
	    $user=$page_session_space->user;
	    $select = $this->select()->setIntegrityCheck(false);
	    $select->distinct();
	    $select->from($this->_name);
	    $select->where('email=?',$user->email);
	    $select->order('date desc');
	    if(isset($options['where']) && count($options['where'])) {
			$select->where($options['where']['field'].' = "'.$options['where']['value'].'"');
		}
		
		if(isset($options['like']) && count($options['like'])) {
			$select->where($options['like']['field'].' LIKE "%'.$options['like']['value'].'%"');
		}
		
		if(isset($options['offset']) && isset($options['limit'])) {
			$select->limit($options['limit'], $options['offset']);
		}
	    $rows = $this->fetchAll($select);
	    $rows = $rows->toArray();

	    $data = array();
	    foreach($rows as $key => $value) {
	        if(!isset($data[$value["id"]])) {
	            $data[$value["id"]]["id"] = $value["id"];
	            $data[$value["id"]]["email"] = $value["email"];
	            $data[$value["id"]]["status"] = $value["status"];
	            $data[$value["id"]]["date"] = $value["date"];
	            $data[$value["id"]]["ip"] = $value["ip"];
	            $data[$value["id"]]["host"] = $value["host"];
	        }
	    }

	    return $data;

	}
    public function updateLogoutDate(){
    	   $page_session_space = new Zend_Session_Namespace('page');
           $user=$page_session_space->user;
    	   $select=$this->select();
    	   $select->where('email=?',$user->email);
    	   $select->order('date desc');
    	   $select->limit(1);
    	   $row =$this->fetchRow($select);
    	   if($row){
    	      $row=$row->toArray();
    	      $data['date_logout']=new Zend_Db_Expr('NOW()');
    	      $id = (int)$row['id'];
    	      $this->updateLog($id, $data);
    	   }else{
    	      return false;
    	    
    	   }
    	 
    }
    /*
     *  Sprawdzamy najpierw czy 3 ostatnie logowania , jeżeli wszystkie są nie poprawne blokujemy na 15
     *  Sprawdzamy pozniej kolejne 2 logowania , jeżeli są nie poprawne blokujemy na 24h 
     */
    public function checkAccessToLogin($email,$ip){
                $res = $this->getLastRecord($email, $ip ,1);
                if($res){
                    if($res['blockade_ip'] == '1'){
                        if(strtotime($res['date_blokade'] .'+ 15 minutes') < strtotime('now')){
                            return true;
                        }else{
                            return false;
                        }
                    }elseif($res['blockade_ip'] == '2'){
                        if(strtotime($res['date_blokade'] .'+ 24 hours') < strtotime('now')){
                            return true;
                        }else{
                            return false;
                        }
                    }else{
                        $result = $this->getLastRecords($email, $ip ,5);
                        if($result){
                            $block = 0;
                            foreach($result as $key => $value){
                                if($id == 0){
                                    $id = $value['id'];
                                }
                                if($value['status'] == 0){
                                    $count++;
                                }
                                if($value['blockade_ip'] == '1'){
                                    $block = 1;
                                }
                                if($value['status'] == 1){
                                    $count = 0;
                                    $block = 0;
                                }
                            }
                            if($count == 5 && $block == 0){
                                $data['blockade_ip']=1;
                                $data['date_blokade']=new Zend_Db_Expr('now()');
                                $this->updateLog($id,$data);
                                return false;
                            }elseif($block > 0){
                                $result = $this->getLastRecords($email, $ip ,8);
                                if($result){
                                    foreach($result as $key => $value){
                                        if($id == 0){
                                            $id = $value['id'];
                                        }
                                        if($value['status'] == 0){
                                            $count++;
                                        }
                                        if($value['status'] == 1){
                                            $count = 0;
                                        }
                                    }
                                    if($count == 8){
                                        $data['blockade_ip']=2;
                                        $data['date_blokade']=new Zend_Db_Expr('now()');
                                        $this->updateLog($id,$data);
                                        return false;
                                    }
                                }else{
                                    return true;
                                }
                            }else{
                                return true;
                            }
                            
                        }
                    }
                }else{
                    return true;
                }
        
    }
    
    public function getLastRecord($email,$ip,$limit){
        $select = $this->select()->setIntegrityCheck(false);
        $select->from($this->_name,array('status','date as date_login','blockade_ip','id','date_blokade'));
        $select->where('email=?',$email);
        $select->where('ip=?',$ip);
        $select->order('date desc');
        $select->limit($limit);
        $row =  $this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
           return $row->toArray();
        }
        return false;
    }
    public function getLastRecords($email,$ip,$limit){
        $select = $this->select()->setIntegrityCheck(false);
        $select->from($this->_name,array('status','date as date_login','blockade_ip','id','date_blokade'));
        $select->where('email=?',$email);
        $select->where('ip=?',$ip);
        $select->order('date desc');
        $select->limit($limit);
        $row =  $this->fetchAll($select);
        if($row instanceof Zend_Db_Table_Rowset){
           $return = array();
           $return = array_reverse($row->toArray());
           return $return;
        }
        return false;
    }




}

?>
