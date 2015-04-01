<?php
/**
 * Model klasy ankiet - odpowiedzi na pytania 
 * @author Mariusz Filipkowski
 *
 */

class Application_Model_DbTable_Surveyanswer extends Zend_Db_Table_Abstract
{

	protected $_name = 'survey_answers';

	public function addSurveyAnswer(array $data)
	{
		return $this->insert($data);
	}

	public function updateSurveyAnswer($id, array $data)
	{
		return $this->update($data, 'id = '. (int)$id);
	}

	public function deleteSurveyAnswerByIdSurvey($id_survey)
	{
		$this->delete('id_survey =' . (int)$id_survey);
	}
	public function getAnswerBySurvayElement($value){
	    $select = $this->select();
	    $select->from($this->_name,array('answer','id_survey_element','id'));
	    $select->where($this->_name.'.id_survey_element=?',$value['id']);
	    $rows = $this->fetchAll($select);
	    if($rows instanceof Zend_Db_Table_Rowset){
	        $rows = $rows->toArray();
	        if($value['type'] == 3){
	            $count = array();
	            $options = explode(chr(10),$value['option_question']);
	            foreach($options as $key_count => $value_count){
	                $count[$key_count]=0;
	            }
	            foreach($rows as $key => $value_answer){
	                $answer = unserialize($value_answer['answer']);
	               if($value['type_question'] == 1){
	                    $count[$answer]=$count[$answer]+1;
	               }elseif($value['type_question']==2){
	                   foreach($answer as $key_answers => $value_answers){
	                      $count[$value_answers]=$count[$value_answers]+1; 
	                   }
	               }
	               $rows=$count;
	            }
	        }
	        
	        return $rows;
	    }
	    return false;
	}
	public function getAnswerUserBySurvayElement($value,$idUser){
	    $select = $this->select();
	    $select->from($this->_name,array('answer','id_survey_element','id'));
	    $select->where($this->_name.'.id_survey_element=?',$value['id']);
	    $select->where($this->_name.'.id_user=?',$idUser);
	    $rows = $this->fetchRow($select);
	    if($rows instanceof Zend_Db_Table_Row){
	        $rows = $rows->toArray();
            return unserialize($rows['answer']);

	    }
	    return false;
	}
    public function countSurvay($id_survey_element){
	    $select = $this->select();
	    $select->from($this->_name,array('answer','id_survey_element','id','count(id) as num'));
	    $select->where($this->_name.'.id_survey_element=?',$id_survey_element);
	    $row = $this->fetchRow($select);
	    if($row instanceof Zend_Db_Table_Row){
	        return $row->toArray();
	    }
	    return false;
	}
  
}

?>
