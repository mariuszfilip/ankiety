<?php
/**
 * Model klasy uzytkownikow
 * @author gustaf
 *
 */

class Application_Model_DbTable_SurveyUser extends Zend_Db_Table_Abstract
{

    protected $_name = 'survey_user_access';

    public function usersUnsetSurvey($id_survey){
        return $this->update(array('status'=>0),'id='.$id_survey);
    }

    public function saveUsers($id_survey, $id_user,$status){
        $status = intval($status);
        $aExist = $this->checkIfExist($id_survey, $id_user);
        if($aExist){
            return $this->update(array('id_user'=>$id_user,'id_survey'=>$id_survey,'status'=>$status),'id='.$aExist['id']);
        }else{
            return $this->insert(array('id_user'=>$id_user,'id_survey'=>$id_survey,'status'=>$status));
        }
    }

    public function checkIfExist($id_survey, $id_user){
        $select = $this->select();
        $select->where('id_survey=?',$id_survey);
        $select->where('id_user=?',$id_user);
        $row = $this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
            return $row->toArray();
        }
        return false;
    }

    public function getActiveUserSurvey($id_survey){
        $select = $this->select();
        $select->where('id_survey=?',$id_survey);
        $select->where('status=?',1);
       // echo $select;
        $rows = $this->fetchAll($select);
        if($rows instanceof Zend_Db_Table_Rowset){
            return $rows->toArray();
        }
        return false;
    }

    public function getActiveUserSurveyAndNotConfirm($id_survey){
        $select = $this->select();
        $select->where('id_survey=?',$id_survey);
        $select->where('status=?',1);
        // echo $select;
        $rows = $this->fetchAll($select);
        if($rows instanceof Zend_Db_Table_Rowset){
            return $rows->toArray();
        }
        return false;
    }

    public function checkIfUserExistInSurvey($id_survey, $id_user){
        $select = $this->select();
        $select->where('id_survey=?',$id_survey);
        $select->where('id_user=?',$id_user);
        $select->where('status=?',1);
        $row = $this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
            return $row->toArray();
        }
        return false;
    }


}

?>
