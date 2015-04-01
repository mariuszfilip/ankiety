<?php
/**
 * Model klasy uzytkownikow
 * @author gustaf
 *
 */

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{

    protected $_name = 'user';

    public function addUser(array $data)
    {
        return $this->insert($data);
    }

    public function updateUser($id, array $data)
    {
        return $this->update($data, 'id = '. (int)$id);
    }

    public function deleteUser($id)
    {
        $this->delete('id =' . (int)$id);
    }

    public function setUserGroup($user_id, $group_id)
    {
        $this->query("insert into user_group (user_id, group_id) values ((int)$user_id, (int)$group_id)");
    }

    public function getUser($id)
    {
        $id = (int)$id;
        //$page_session_space = new Zend_Session_Namespace('page');
        //$user=$page_session_space->user;
        $select = $this->select()->setIntegrityCheck(false);
        $select->from("user", "user.*");
        $select->where("user.id = ? ", $id);

        if($subscriber = $this->fetchRow($select)) {
            return $subscriber->toArray();
        }
        else {
            return false;
        }
    }


    public function getSingleWithUsername($username)
    {
        $select = $this->select();
        $where = $this->getAdapter()->quoteInto('username = ?', $username);
        $select->where($where);
        return $this->fetchRow($select);
    }

    public function getSingleWithEmail($email)
    {
        $select = $this->select();
        $where = $this->getAdapter()->quoteInto('email = ?', $email);
        $select->where($where);
        return $this->fetchRow($select);
    }

    public function getSingleWithEmailHash($hash)
    {
        $select = $this->select();
        $where = $this->getAdapter()->quoteInto('SHA1(email) = ?', $hash);
        $select->where($where);
        return $this->fetchRow($select);
    }

    public function fetchAllUser($options) {
        $page_session_space = new Zend_Session_Namespace('page');
        $user=$page_session_space->user;
        $select = $this->select()->setIntegrityCheck(false);
        $select->distinct();
        $select->from("user");
        $select->order($options['field'].' '.$options['dir']);
        if(count($options['where'])) {
            $select->where($options['where']['field'].' = '.$options['where']['value']);
        }

       // $select->joinLEFT('city','city.id = user.city_id', 'city.name as city')
        //->joinLeft('user_groupacl','user_groupacl.user_id = user.id', "")
        //->joinLeft('groupacl','user_groupacl.groupacl_id = groupacl.id', "groupacl.name as group");

        $rows = $this->fetchAll($select);
        $rows = $rows->toArray();

        $data = array();
        foreach($rows as $key => $value) {
            if(!isset($data[$value["id"]])) {
                $data[$value["id"]]["id"] = $value["id"];
                $data[$value["id"]]["first_name"] = $value["first_name"];
                $data[$value["id"]]["last_name"] = $value["last_name"];
                $data[$value["id"]]["email"] = $value["email"];
                $data[$value["id"]]["status"] = $value["status"];
                $data[$value["id"]]["city"][$value["city"]] = $value["city"];
                $data[$value["id"]]["group"][$value["group"]] = $value["group"];
            }
            else {
                $data[$value["id"]]["city"][$value["city"]] = $value["city"];
                $data[$value["id"]]["group"][$value["group"]] = $value["group"];
            }

        }

        return $data;

    }

    public function clearUserQuota(){
        $data = array('emails_sent' => 0);

        return $this->update($data, array());
    }


    public function fetchAllUserClient($options) {
        $page_session_space = new Zend_Session_Namespace('page');
        $user=$page_session_space->user;
        $select = $this->select()->setIntegrityCheck(false);
        $select->distinct();
        $select->from("user");
        $rows = $this->fetchAll($select);
        //$rows = $rows->toArray();
        //var_dump($rows);
        return $rows;

    }
    public function fetchAllActiveUser() {

        $select = $this->select()->setIntegrityCheck(false);
        $select->distinct();
        $select->from("user");
        $select->where('status=?',1);
        $select->where('active_client=?',1);

        $rows = $this->fetchAll($select);
        return $rows;

    }
    public function getUserRegisterConfirm($id)
    {
        $id = (int)$id;
        $select = $this->select()->setIntegrityCheck(false);
        $select->from("user", "user.*");
        $select->where("user.id = ? ", $id);

        if($subscriber = $this->fetchRow($select)) {
            return $subscriber->toArray();
        }
        else {
            return false;
        }
    }
    public function checkOldPassword($password)
    {
        $session_space = new Zend_Session_Namespace('page');
        $id= $session_space->user->id;
        $select = $this->select()->setIntegrityCheck(false);
        $select->where("password = ? ", $password);
        $select->where("id = ? ", $id);
        if($subscriber = $this->fetchRow($select)) {
            return $subscriber->toArray();
        }
        else {
            return false;
        }
    }
    public function checkPasswordHash($id_user){
        $select = $this->select();
        $select->where('id=?',$id_user);
        $select->where('password_hash=?','0');
        $row = $this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
            return $row->toArray();
        }
        return false;
    }
    public function getUserByPasswordHash($hash){
        
        $select = $this->select();
        $select->where('password_hash=?',$hash);
        $row = $this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
            $row = $row->toArray();
            if($this->checkPasswordHash($row['id']) == false){
				return $row;
			}else{
				return false;
			}
        }
        return false; 
        
    }
    public function getUserLogin($id){
            $id = (int)$id;

        $select = $this->select()->setIntegrityCheck(false);
        $select->from("user", "user.*");
        $select->where("user.id = ? ", $id);

        if($user = $this->fetchRow($select)) {
            return $user;
        }
        else {
            return false;
        }
        
        
    }


    public function getUserByHash($hash){
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
