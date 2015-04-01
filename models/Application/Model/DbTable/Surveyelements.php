<?php
/**
 * Model klasy ankiet - elementy ankiety
 * @author Mariusz Filipkowski
 *
 */

class Application_Model_DbTable_Surveyelements extends Zend_Db_Table_Abstract
{

	protected $_name = 'survey_elements';

	public function addSurveyElement(array $data)
	{
		return $this->insert($data);
	}

	public function updateSurveyElement($id, array $data)
	{
		return $this->update($data, 'id = '. (int)$id);
	}

	public function deleteSurveyElement($id)
	{
		$this->delete('id =' . (int)$id);
	}
    public function deleteSurveyElementsByIdSurvey($id_survey)
	{
		$this->delete('id_survey =' . (int)$id_survey);
	}

    public function getSurveyElementsByIdsurvey($id_survey){
	    $select = $this->select();
	    $select->where('id_survey=?',(int)$id_survey);
        $select->order('priority');
	    $rows = $this->fetchAll($select);
	    if($rows instanceof Zend_Db_Table_Rowset){
	        return $rows->toArray();
	    }
	    return false;
	    
	}
	public function getValuesToPopulate($id_survey){
	    $page_session_space = new Zend_Session_Namespace('page');
		$user=$page_session_space->user;
	    $select = $this->select();
	    $select->where('id_survey=?',(int)$id_survey);
	    $rows = $this->fetchAll($select);
	    if($rows instanceof Zend_Db_Table_Rowset){
	        $result=array();
	        $rows = $rows->toArray();
	        foreach($rows as $key => $value){
	            $id = $value['id'];
	            $result['header'.$id] = $value['header'];
	            $result['type_question'.$id] = $value['type_question'];
	            $result['option_question'.$id] = $value['option_question'];
                $result['positive_answer'.$id] = $value['positive_answer'];
	            $result['target'.$id] = $value['target'];
                $result['priority'.$id] = $value['priority'];
                $result['pionts_for_answer'.$id] = $value['pionts_for_answer'];
	            
	        }
	        return $result;
	    }
	    return false;
	    
	    
	}
}

?>
