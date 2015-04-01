<?php

/**
 * Zarządzanie ankietami 
 * @author Mariusz Filipkowski
 *
 */
class SurveyController extends Zend_Controller_Action
{
	static public $labelTable = array("id" => "Id","name" => "Name","create_date"=>"Data utworzenia");

	static public $actionTable = array("edit" => "Edytuj", "delete" => "Usuń");

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->messages = $this->_flashMessenger->getMessages();
	}

	public function preDispatch()
	{


	    if ($this->getRequest()->getParam('action') != "fill" && $this->getRequest()->getParam('action') != "stopfill"
            && $this->getRequest()->getParam('action') != "end" )  {
	        $page_session_space = new Zend_Session_Namespace('page');
	        if (!isset($page_session_space->user) || $page_session_space->user==null) {
	            $this->_redirect('/auth/login');
	        }
	    }
		
	}
	public function indexAction()
	{
		$this->_forward("list");
		
	}

	/**
	 * Nowa ankieta
	 *
	 * @author Mariusz Filipkowski
	 * @param
	 * @return bool
	 */
	public function addAction()
	{
	    if ($this->getRequest()->isXmlHttpRequest()) {
    	   $this->_helper->layout()->disableLayout();
		   if ($this->getRequest()->isPost())
				$this->_helper->viewRenderer->setNoRender(true);
		}
		$this->view->title = $this->view->translate('add.new.survey');
		$this->view->headTitle($this->view->title);
		$form = new Application_Form_Survey();
		$this->view->form = $form;
		$config = Zend_Registry::get('config');
		$this->view->syspath = $config->system->path;
         $survey = new Application_Model_DbTable_Survey();
         if ($this->getRequest()->isPost()) {
             $formData = $this->getRequest()->getPost();
             $result= array();
             try{
                 if ($form->isValid($formData)) {
                     if ($survey->checkExist($formData['name']) != null) {
                         $result['result']='error';
                         $result['messages'] = $this->view->translate('survey.already.taken');
                     }else{
                         $data['name'] = $form->getValue('name');
                         $data['min_points'] = floatval($form->getValue('min_points'));
                         $data['date_start_availability'] = $form->getValue('date_start_availability');
                         $data['date_finish_availability'] = $form->getValue('date_finish_availability');
                         $data['duration'] = intval($form->getValue('duration'));
                         $data['hash'] = $data['hash'] = md5($form->getValue('name'));
                          
                         $survey->addSurvey($data);
                         $this->view->success = $this->view->translate('survey.added');
                         $this->_flashMessenger->addMessage($this->view->translate('survey.added'));
                         $result['result']='success';
                         $result['messages']=$this->view->translate('survey.added');
                         $this->_redirect('survey');
                     }
                      
                 } else {
                     $result['result']='error';
                     $html='';
                     foreach($form->getMessages() as $key => $value){
                         foreach($value as $message_key => $message_value){
                             $key = str_replace("_",".",$key);
                             $html.="<div class='errors'><strong>".$this->view->translate($key.'.survey')."</strong>: $message_value</div>";
                             $this->_flashMessenger->addMessage("<div class='errors'><strong>".$this->view->translate($key.'.survey')."</strong>: $message_value</div>");
                         }
                     }
                     $result['messages']=$html;
                     $this->_redirect('survey/add');
                 }
             }catch(Exception $e){
                 $result['result']='failed';
                 $result['messages']=$e;
             }
             $this->_flashMessenger->addMessage($result['messages']);
             $this->_redirect('survey/add');
         }
	}

	/**
	 * Edycja ankiety - dodawanie elemntów ankiety
	 *
	 * @author Mariusz Filipkowski
	 * @param int $id
	 * @return bool
	 */
	public function editAction()
	{

		if ($this->getRequest()->isXmlHttpRequest()) {
    	   $this->_helper->layout()->disableLayout();
		   if ($this->getRequest()->isPost()){
				$this->_helper->viewRenderer->setNoRender(true);
		   }
		}
		$this->view->title = $this->view->translate('edit.survey');
		$this->view->headTitle($this->view->title);

		$form = new Application_Form_Surveyelement();
		$config = Zend_Registry::get('config');
		$this->view->syspath = $config->system->path;
		$this->view->form = $form;

		if ($this->getRequest()->isPost()) {
		    $formData = $this->getRequest()->getPost();
		    $id_survay = $formData['id'];
		    try{
		    if($id_survay != ''){
		        $survey_element = new Application_Model_DbTable_Surveyelements();
		        $survey_element_list = $survey_element->getSurveyElementsByIdsurvey($id_survay);
		        if($survey_element_list){
		            foreach($survey_element_list as $key => $value){
		                $header_name = 'header'.$value['id'];
		                if(isset($formData[$header_name]) and $formData[$header_name] != ''){
		                    $type = $value['type'];
		                    $id = $value['id'];

		                    switch ((int)$type) {
		                        case 1:
		                            $target='target'.$id;
                                    $priority='priority'.$id;
		                            $data=array();
		                            $data['header']=$formData[$header_name];
		                            $data['target']=$formData[$target];
                                    $data['priority']=$formData[$priority];
		                            $survey_element->updateSurveyElement($value['id'],$data);
		                            break;
		                        case 2:
		                            $type_question='type_question'.$id;
                                    $priority='priority'.$id;
		                            $target='target'.$id;
		                            $data=array();
		                            $data['header']=$formData[$header_name];
		                            $data['type_question']=$formData[$type_question];
		                            $data['target']=$formData[$target];
                                    $data['priority']=$formData[$priority];
		                            $survey_element->updateSurveyElement($value['id'],$data);
		                            break;
		                        case 3:
		                            $type_question='type_question'.$id;
		                            $option_question='option_question'.$id;
                                    $positive_answer='positive_answer'.$id;
                                    $priority='priority'.$id;
                                    $pionts_for_answer = 'pionts_for_answer'.$id;
		                            $data=array();
		                            $data['header']=$formData[$header_name];
		                            $data['type_question']=$formData[$type_question];
		                            $data['option_question']=$formData[$option_question];
                                    $data['positive_answer']=$formData[$positive_answer];
                                    $data['priority']=$formData[$priority];
                                    $data['pionts_for_answer']=$formData[$pionts_for_answer];
		                            $survey_element->updateSurveyElement($value['id'],$data);
		                            break;
		                             
		                    }
		                    $result['result']='success';
		                    $result['messages']=$this->view->translate('survey.modified');
		                }

		            }

		        }
		    }         
		    }catch(Exception $e){
                 $result['result']='failed';
                 $result['messages']=$e;
             }
             echo json_encode($result);

		} else {
		    $id = $this->_getParam('id', 0);
		    if ($id > 0) {
		        $survey = new Application_Model_DbTable_Survey();
	            $survey_result = $survey->getSurvey($id);
	            if($survey_result){
	                $survey_element = new Application_Model_DbTable_Surveyelements();
	                $data = $survey_element->getValuesToPopulate($id);
	                $data['id']=$id;
	                $form->populate($data);
	            }
		    }
		}
	}
    
	
	/**
	 * Deletes user element
	 *
	 * @author Michał Piasecki <michal.piasecki@e-surf.pl>
	 * @param int $id
	 * @return bool
	 */
	public function deleteAction()
	{
	    if ($this->getRequest()->isXmlHttpRequest()) {
    	   $this->_helper->layout()->disableLayout();
		   if ($this->getRequest()->isPost())
				$this->_helper->viewRenderer->setNoRender(true);
		}
		$this->view->title = $this->view->translate('user.deleted');
		$this->view->headTitle($this->view->title);
		if ($this->getRequest()->isPost()) {
			$del = $this->getRequest()->getPost('del');
			$selected = $this->getRequest()->getPost('selected');
			$survey = new Application_Model_DbTable_Survey();
			$elements = new Application_Model_DbTable_Surveyelements();
			$answer = new Application_Model_DbTable_Surveyanswer();
			if ($del != null) {
				$id = $this->getRequest()->getPost('id');
				$survey->deleteSurvey($id);
				$answer->deleteSurveyAnswerByIdSurvey($id);
				$elements->deleteSurveyElementsByIdSurvey($id);
			}elseif($selected){
			    $temp = explode(",", $selected);
                foreach($temp as $key => $value) {
                    if($value) {
                        $survey->deleteSurvey($value);
                        $answer->deleteSurveyAnswerByIdSurvey($value);
                        $elements->deleteSurveyElementsByIdSurvey($value);
                    }
                }
			}
			$this->_flashMessenger->addMessage($this->view->translate('user.deleted'));
			$this->_helper->redirector('index');
		}
		else {
			$id = $this->_getParam('id', 0);
			$survey = new Application_Model_DbTable_Survey();
			$this->view->user = $survey->getSurvey($id);
			$this->_flashMessenger->addMessage($this->view->translate('user.not.deleted'));
		}
	}

	/**
	 * Lista ankiet
	 *
	 * @author Mariusz Filipkowski
	 * @param
	 * @return bool
	 */
	public function listAction()
	{
	    
        $config = Zend_Registry::get('config');
		$this->view->syspath = $config->system->path;
		$page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : '1';
		$sortField = $this->getRequest()->getParam('sort') ? $this->getRequest()->getParam('sort') : 'id';
		$sortDir = $this->getRequest()->getParam('direction') ? $this->getRequest()->getParam('direction') : 'asc';

		$where = array();
		if ($this->getRequest()->getParam('g')) {
			$where['field'] = "groupacl.id";
			$where['value'] = $this->getRequest()->getParam('g');
		}

		$survey = new Application_Model_DbTable_Survey();

		$list = $this->view->ListData($survey->getListSurvey(array("field" => $sortField,"dir" => $sortDir, "where" => $where)), self::$labelTable, self::$actionTable, "user");

		if(isset($_POST["action"]) && $_POST["action"] == "listdata") {
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			echo $list;
			return;
		}

		$this->view->title = $this->view->translate('survey');
		$this->view->headTitle($this->view->title);

		$this->view->page = $page;
		$this->view->sort = $sortField;
		$this->view->direction = $sortDir;
		$this->view->messages = $this->_flashMessenger->getMessages();

		$this->view->list = $list;
	}
	public function getListAction() {
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender();
	     
	    $columnsMap = array(
	    0 => 'id',
	    1 => 'name',
	    2 => 'create_date'
	    );
	     
	    $page_session_space = new Zend_Session_Namespace('page');
	    $user = $page_session_space->user;
	    $limit = $this->getRequest()->getParam('iDisplayLength', 20);
	    $offset = $this->getRequest()->getParam('iDisplayStart', 0);
	     
	    $surveyModel = new Application_Model_DbTable_Survey();
	    $select = $surveyModel->select()->setIntegrityCheck(false);
	    $select->distinct(true);
	    $select->from('survey');
   		//$select->where('survey.id_client=?',$user->id_client);
   			 
   			$sortingCols = $this->getRequest()->getParam('iSortingCols', 0);
   			if($sortingCols != 0) {
   			    $sortCol = (int)$this->getRequest()->getParam('iSortCol_0');
   			    if(!array_key_exists($sortCol, $columnsMap)) {
   			        $sortCol = 3;
   			    }
   			    $sortDir = $this->getRequest()->getParam('sSortDir_0');
   			    $select->order($columnsMap[$sortCol] .' '. $sortDir);
   			     
   			}
   			 
   			$searchString = $this->getRequest()->getParam('sSearch');
   			if(!empty($searchString)) {
   			    $select->where("survey.name like '%$searchString%'");
   			}

   			$filteredRows = $surveyModel->fetchAll($select);
   			 
   			$select->limit($limit, $offset);
   			$uSelect = $surveyModel->select()->setIntegrityCheck(false);
   			$allRows = $surveyModel->fetchAll($uSelect);
   			$config = Zend_Registry::get('config');
   			$rows = $surveyModel->fetchAll($select);
            $oUserSruvey = new Application_Model_DbTable_SurveyUser();
   			if($rows instanceof Zend_Db_Table_Rowset){
   			    $rows = $rows->toArray();
   			    foreach($rows as $key => $value){

                    $aRows = $oUserSruvey->getActiveUserSurvey($value['id']);
   			        $rows[$key]['users_count']=count($aRows);
   			    }
   			}else{
   			    $rows=array();
   			}
           
   			 
   			$answer['iTotalRecords'] = $allRows->count();
   			$answer['iTotalDisplayRecords'] = $filteredRows->count();
   			$answer['sEcho'] = (int)$this->getRequest()->getParam('sEcho');
   			$answer['aaData'] = $rows ? $rows : array();
   			 
   			echo Zend_Json::encode($answer);
	}
	
	public function addelementAction(){
	    $config = Zend_Registry::get('config');
		$this->view->syspath = $config->system->path;
		$syspath = $config->system->path;
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender();
	    $elementDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRight')),
			'Label',
			array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRow overflowHidden')),
         );
         $standardGroupDecorator = array(
			'FormElements',
			array('HtmlTag', array()),
			'Fieldset'
      );
	    if ($this->getRequest()->isPost()) {
	        $id = $this->getRequest()->getPost('id');
	        $type = $this->getRequest()->getPost('type');
	        $survey = new Application_Model_DbTable_Survey();
	        $survey_result = $survey->getSurvey($id);
	        if($survey_result && $id != '' && $type != ''){
	            $survey_element = new Application_Model_DbTable_Surveyelements();
	            $data['type']=$type;
	            $data['id_survey']=$id;
	            $id = $survey_element->addSurveyElement($data);
	            switch ($type) {
	                case 1:
	                    $header = new Zend_Form_Element_Text("header$id");
                        $header->setRequired(true)->setLabel($this->view->translate('text.header'))->setDecorators($elementDecorators);
                        $target = new Zend_Form_Element_Text("target$id");
                        $target->setLabel($this->view->translate('target'))->setDecorators($elementDecorators);
                        echo '<fieldset id="fieldset-dane" class="widget"><div class="title"><img class="titleIcon" alt="" src="'.$syspath.'/images/icons/dark/pencil.png"><h6>'.$this->view->translate('header').'</h6></div>';
                        echo $header->__toString();
                        echo $target->__toString();

                        echo '</fieldset>';
	                    break;
	                case 2:
	                    $type_question = new Zend_Form_Element_Select("type_question$id");
                        $type_question->setRequired(true)->setLabel($this->view->translate('type'))->setDecorators($elementDecorators);
                        $type_question->setMultiOptions(array('1'=>$this->view->translate('question.open.one.line'),'2'=>$this->view->translate('question.open.many.line')));
	                    $header = new Zend_Form_Element_Text("header$id");
                        $header->setRequired(true)->setLabel($this->view->translate('question'))->setDecorators($elementDecorators);
                        $target = new Zend_Form_Element_Text("target$id");
                        $target->setLabel($this->view->translate('explanation.optional'))->setDecorators($elementDecorators);
                        echo '<fieldset id="fieldset-dane" class="widget"><div class="title"><img class="titleIcon" alt="" src="'.$syspath.'/images/icons/dark/pencil.png"><h6>'.$this->view->translate('question.open').'</h6></div>';
                        echo $type_question->__toString();
                        echo $header->__toString();
                        echo $target->__toString();
                        echo '</fieldset>';
	                    break;
	                case 3:
	                    $type_question = new Zend_Form_Element_Select("type_question$id");
                        $type_question->setRequired(true)->setLabel($this->view->translate('type'))->setDecorators($elementDecorators);
                        $type_question->setMultiOptions(array('1'=>$this->view->translate('question.close.one.answer'),'2'=>$this->view->translate('question.close.many.answer')));
	                    $header = new Zend_Form_Element_Text("header$id");
                        $header->setRequired(true)->setLabel($this->view->translate('question'))->setDecorators($elementDecorators);
                        $target = new Zend_Form_Element_Textarea("option_question$id");
                        $target->setRequired(false)->setLabel($this->view->translate('option.answer'))->setDecorators($elementDecorators)->setAttrib('COLS', '40')->setAttrib('ROWS', '4');
                        echo '<fieldset id="fieldset-dane" class="widget"><div class="title"><img class="titleIcon" alt="" src="'.$syspath.'/images/icons/dark/pencil.png"><h6>'.$this->view->translate('question.close').'</h6></div>';
                        echo $type_question->__toString();
                        echo $header->__toString();
                        echo $target->__toString();
                        echo '</fieldset>';
	                    break;
	            }
	        
	        }
        }
	}
	
	public function previewAction(){
        $this->_helper->layout()->setLayout('layoutfill');
	    $this->view->title = $this->view->translate('preview.survey');
		$this->view->headTitle($this->view->title);
	    if ($this->getRequest()->isXmlHttpRequest()) {
    	   $this->_helper->layout()->disableLayout();
		   if ($this->getRequest()->isPost())
				$this->_helper->viewRenderer->setNoRender(true);
		}
	    $id = $this->_getParam('id',0);
	    if($id > 0){
	         $survey = new Application_Model_DbTable_Survey();
	         $survey_element = new Application_Model_DbTable_Surveyelements();
	         $survey_result = $survey->getSurvey($id);
	         if($survey_result){
	             $form = new Application_Form_Surveygenerate($id);
	             $form->setActionName('preview');
	             $survey_element_result = $survey_element->getSurveyElementsByIdsurvey($id);
	             $this->view->survey_element = $survey_element_result;
	             $this->view->form = $form;
	         }
	    }
	}

    public function endAction(){
        $this->_helper->layout()->setLayout('layoutfill');


    }
    public function stopfillAction(){
         $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->setLayout('layoutfill');
        if ($this->getRequest()->isPost()) {
        $survey_Time = new Application_Model_DbTable_SurveyTime();
            $page_session_space = new Zend_Session_Namespace('page');
        $res=$survey_Time->stop($page_session_space->user->id, $this->_getParam('survey_id',0));
        }

    }
	public function fillAction(){
		$this->_helper->layout()->setLayout('layoutfill');
	    $hash = $this->_getParam('hash',0);
        $hash_user = $this->_getParam('hash_user',0);
	    
	    if($hash !== 0 && $hash_user !== 0){
            $hash = $this->getRequest()->getParam('hash');
            $oUser = new Application_Model_DbTable_User();

            $aUser = $oUser->getUserByHash($hash_user);
            if(!$aUser){
                echo '<h1>Brak dostepu. Nie ma takiego użytkownika</h1>';
                exit();
            }
            $id_user = $aUser['id'];

                $survey = new Application_Model_DbTable_Survey();
                $survey_element = new Application_Model_DbTable_Surveyelements();
                $survey_Time = new Application_Model_DbTable_SurveyTime();
                $survey_result = $survey->getByHash($hash);

                if($survey_result){

                    $oUserSurvey = new Application_Model_DbTable_SurveyUser();
                    $aUserSurvey = $oUserSurvey->checkIfUserExistInSurvey($survey_result['id'],$aUser['id']);
                        $this->view->error=0;
                        $this->view->msg=0;
                    if(!$aUserSurvey){
                        $this->view->error=1;
                       $this->view->msg='<h1>Brak dostepu</h1>';

                    }
                    $dStartAvaibility = new DateTime($survey_result['date_start_availability']);
                    $dFinishAvaibility = new DateTime($survey_result['date_finish_availability']);
                    $dNow = new DateTime();
                    $intervalStart=$dStartAvaibility->diff($dNow);
                    $intervalEnd=$dFinishAvaibility->diff($dNow);
                    if(($intervalStart->invert == 1) && $this->view->error==0){
                        $this->view->error=1;
                        $this->view->msg='<h1>Jest za wcześnie na rozpoczęcie egzaminu. Poczekaj do '.$survey_result['date_start_availability'].'. Brak dostępu</h1>';
                    }
                    if($intervalEnd->invert == 0 && $this->view->error==0){
                        $this->view->error=1;
                        $this->view->msg='<h1>Jest za póżno na rozpoczęcie egzaminu. Egzamin był do '.$survey_result['date_finish_availability'].'. Brak dostępu</h1>';
                    }



                       $timeDuration = $survey_result['duration'];

                    $form = new Application_Form_Surveygenerate($survey_result['id']);
                    $this->view->min=$timeDuration;
                    $time=$survey_Time->getTime($id_user,$survey_result['id']);

                        if($survey_Time->CzyWypelnil($id_user,$survey_result['id'])){
                            $this->view->error=1;
                            $this->view->msg= '<h1>Egzamin został wcześniej wypełniony</h1>';

                        }

                    if($time==false && $this->view->error == 0){
                        $survey_Time->start($id_user,$survey_result['id']);
                    }else{
                        $this->view->min=$timeDuration-$time;
                    }
                    $form->setActionName('fill');
                    $survey_element_result = $survey_element->getSurveyElementsByIdsurvey($survey_result['id']);
                    $this->view->id = $survey_result['id'];
                    $this->view->form = $form;
                    $this->view->name = $survey_result['name'];

                    if ($this->getRequest()->isPost()) {
                        $formData = $this->getRequest()->getPost();
                        if ($form->isValid($formData)) {
                            $success = 0;
                            $answer = new Application_Model_DbTable_Surveyanswer();
                            foreach($survey_element_result as $key => $value){
                                if($value['type'] != 1 && $value['type'] != 0){
                                    $data=array();
                                    $data['id_survey_element']=(int)$value['id'];
                                    $id_surver_element=(int)$value['id'];
                                    if($value['type'] == 3){
                                        $data['answer']=serialize($form->getValue("answer$id_surver_element"));
                                    }else{
                                        $data['answer']=$form->getValue("answer$id_surver_element");
                                    }
                                    $data['id_survey']=(int)$survey_result['id'];
                                    $data['id_user']=(int)$id_user;
                                    $answer->addSurveyAnswer($data);
                                    $success = 1;
                                }
                            }
                            $aSurveyTime = $survey_Time->pobierzEgzamin($id_user,$survey_result['id']);
                            if($aSurveyTime){
                                $survey_Time->update(array('status'=>0,'stop'=>new Zend_Db_Expr("now()")),'id='.$aSurveyTime['id']);
                            }

                            $this->view->success = $success;

                        }
                    }
                }
	        }
	
	}
	public function reportAction(){
	    if ($this->getRequest()->isXmlHttpRequest()) {
    	   $this->_helper->layout()->disableLayout();
		   if ($this->getRequest()->isPost())
				$this->_helper->viewRenderer->setNoRender(true);
		}
	    $id = $this->_getParam('id',0);
	    if($id > 0){
	       $survey = new Application_Model_DbTable_Survey();
	       $survey_result = $survey->getSurvey($id);
	       if($survey_result){
	            $id_surver_count = 0;
	           	$survey_element = new Application_Model_DbTable_Surveyelements();
		        $survey_element_list = $survey_element->getSurveyElementsByIdsurvey($id);
		        $answer = new Application_Model_DbTable_Surveyanswer();
		        $count = null;
		        if($survey_element_list){
		            foreach($survey_element_list as $key => $value){
		                if($value['type'] != 1){
		                    $id_surver_count = $value['id'];
		                    $result = $answer->getAnswerBySurvayElement($value);
		                    $survey_element_list[$key]['answer']=$result;
		                }

		            }
		        }
		        $count = $answer->countSurvay($id_surver_count);
		        if(!$count){
		            $count=0;
		        }else{
		            $count=$count['num'];
		        }
		        $this->view->survey = $survey_element_list;
		        $this->view->count = $count;
	       
	       }

	    
	    }
	
	}
    public function usersAction(){

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
            if ($this->getRequest()->isPost())
                $this->_helper->viewRenderer->setNoRender(true);

        }
        $this->view->title = 'Przypisz użytkowników do egzaminu';
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_SurveyUser();
        $config = Zend_Registry::get('config');
        $this->view->syspath = $config->system->path;
        $oUserSurvey = new Application_Model_DbTable_SurveyUser();
        $id_survey = $this->_getParam('id_survey',0);
        if ($this->getRequest()->isPost()) {
            $result = array();
            try{
                   $formData = $this->getRequest()->getPost();
                   $oUserSurvey->saveUsers($id_survey,$formData['id_user'],$formData['status']);
                   $result['result']='success';
                   $result['messages']=$this->view->translate('user.added');

            }catch(Exception $e){
                $result['result']='failed';
                $result['messages']=$e;
            }
            echo Zend_Json::encode($result);
            exit();
        }else{
            $aFormPopulate= array();
            $aUsers = $oUserSurvey->getActiveUserSurvey($id_survey);
            $aUsersCheckbox = array();
            foreach($aUsers as $aUser){
                $aUsersCheckbox[$aUser['id_user']]=$aUser['id_user'];
            }

            $aFormPopulate['users'] = $aUsersCheckbox;

            $form->populate($aFormPopulate);
        }
        $oSurwey = new Application_Model_DbTable_Survey();
        $aSurvey = $oSurwey->getSurvey($id_survey);
        $this->view->users = $aUsersCheckbox;
        $this->view->id_survey = $id_survey;
        $this->view->form = $form;
        $this->view->survey = $aSurvey;
    }


    public function tellAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $oSurvey = new Application_Model_DbTable_Survey();
        $oUser = new Application_Model_DbTable_User();
        $oUserSurvey = new Application_Model_DbTable_SurveyUser();
        $id_survey = $this->_getParam('id_survey',0);
        $aUsersSurvey = $oUserSurvey->getActiveUserSurveyAndNotConfirm($id_survey);
        $count = 0;
        $aSurvey = $oSurvey->getSurvey($id_survey);
        if($aSurvey){
            foreach($aUsersSurvey as $aUserSurvey){

                $aUser = $oUser->getUser($aUserSurvey['id_user']);
                $count++;
                $config = Zend_Registry::get('config');
                var_dump($config);
                $mail=new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPDebug  = 2;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = $config->mailserver->smtpsecure;
                $mail->Host = $config->mailserver->host;
                $mail->Port =  $config->mailserver->port;
                $mail->Username = $config->mailserver->username;
                $mail->Password = $config->mailserver->password;
                $mail->CharSet = "UTF-8";
                $mail->FromName = $config->mail->sender;
                $mail->From = $config->mail->email;
                $mail->Subject =$aSurvey['name'];
                $mail->IsHTML(true);

                $config = Zend_Registry::get('config');
                $link=$config->system->path.'survey/fill/hash/'.$aSurvey['hash'].'/hash_user/'.$aUser['hash'];

                $mail->Body = '
                Witam!<br/>
                Informujemy że od '.$aSurvey['date_start_availability'].' do '.$aSurvey['date_finish_availability'].' będzie dostępny egzamin '.$aSurvey['name'].'.<br/>
                Na rozwiązanie egzaminu jest '.$aSurvey['duration'].' minut, a czas liczy się od momentu kliknięcia w link.<br/>
                Do pozytywnego zaliczenia egzaminu należy otrzymać przynajmniej 70% punktów. W przypadku awarii, utraty połączenia z Internetem lub innych niezależnych zdarzeń można ponownie wejść w link.<br/>
                <br/>
                                '.$link.'
                                 <br/>
                                  <br/>
                Powodzenia na egzaminie!
                ';

                $mail->AddAddress($aUser["email"]);
                try {
                    $mail->send();
                    $oUserSurvey->update(array('confirm_email'=>1),'id='.$aUserSurvey['id']);
                }
                catch (phpmailerException $e) {
                    echo $e->errorMessage(); //Pretty error messages from PHPMailer
                    exit();
                }
                catch (Exception $ex) {
                    echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
                    var_dump($mail);
                    exit();
                }

            }
        }
        $this->_flashMessenger->addMessage('O egzaminie poinformowano '.$count.' Użytkowników');
        $this->_redirect('survey/users/id_survey/'.$id_survey);
    }


    /**
     * Nowa ankieta
     *
     * @author Mariusz Filipkowski
     * @param
     * @return bool
     */
    public function editinfoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
            if ($this->getRequest()->isPost())
                $this->_helper->viewRenderer->setNoRender(true);
        }
        $id_survey = $this->_getParam('id_survey',0);
        $this->view->title = 'Edytuj egzamin';
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Survey();
        $this->view->form = $form;
        $config = Zend_Registry::get('config');
        $this->view->syspath = $config->system->path;
        $survey = new Application_Model_DbTable_Survey();
        $aSurvey = $survey->getSurvey($id_survey);
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $result= array();
            try{
                if ($form->isValid($formData)) {

                        $data['name'] = $form->getValue('name');
                        $data['date_start_availability'] = date('Y-m-d',strtotime($form->getValue('date_start_availability')));
                        $data['date_finish_availability'] = date('Y-m-d',strtotime($form->getValue('date_finish_availability')));
                        $data['duration'] = intval($form->getValue('duration'));
                        $data['min_points'] = floatval($form->getValue('min_points'));
                        $survey->updateSurvey($id_survey,$data);
                        $this->view->success ='Egzamin zaktualizowano poprawnie';
                        $this->_flashMessenger->addMessage('Egzamin zaktualizowano poprawnie');
                        $result['result']='success';
                        $result['messages']=$this->view->translate('survey.added');
                        $this->_redirect('survey/editinfo/id_survey/'.$id_survey);


                } else {
                    $result['result']='error';
                    $html='';
                    foreach($form->getMessages() as $key => $value){
                        foreach($value as $message_key => $message_value){
                            $key = str_replace("_",".",$key);
                            $html.="<div class='errors'><strong>".$this->view->translate($key.'.survey')."</strong>: $message_value</div>";
                            $this->_flashMessenger->addMessage("<div class='errors'><strong>".$this->view->translate($key.'.survey')."</strong>: $message_value</div>");
                        }
                    }
                    $result['messages']=$html;
                    $this->_redirect('survey/editinfo/id_survey/'.$id_survey);
                }
            }catch(Exception $e){
                $result['result']='failed';
                $result['messages']=$e;
            }
            $this->_flashMessenger->addMessage($result['messages']);
            $this->_redirect('survey/editinfo/id_survey/'.$id_survey);
        }else{
            $form->populate($aSurvey);
        }
    }


    public function userslistAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $id_survey = $this->_getParam('id_survey',0);
        $columnsMap = array(
            0 => 'id',
            1 => 'first_name',
            2 => 'last_name',
            3 => 'email',
            4 => 'status'
        );

        $page_session_space = new Zend_Session_Namespace('page');
        $user = $page_session_space->user;
        $limit = $this->getRequest()->getParam('iDisplayLength', 20);
        $offset = $this->getRequest()->getParam('iDisplayStart', 0);

        $userModel = new Application_Model_DbTable_User();
        $select = $userModel->select()->setIntegrityCheck(false);
        $select->distinct(true);
        $select->from('user', array(
            'id',
            'first_name',
            'last_name',
            'email',
            'status'

        ));
        //$select->where('user.id_client=?',$user->id_client);

        $sortingCols = $this->getRequest()->getParam('iSortingCols', 0);
        if($sortingCols != 0) {
            $sortCol = (int)$this->getRequest()->getParam('iSortCol_0');
            if(!array_key_exists($sortCol, $columnsMap)) {
                $sortCol = 3;
            }
            $sortDir = $this->getRequest()->getParam('sSortDir_0');
            $select->order($columnsMap[$sortCol] .' '. $sortDir);

        }

        $searchString = $this->getRequest()->getParam('sSearch');
        if(!empty($searchString)) {
            $select->where("user.first_name like '%$searchString%' or user.last_name like '%$searchString%' or user.email like '%$searchString%'");
        }

        //$answer['sql'] = $select->__toString();

        $filteredRows = $userModel->fetchAll($select);

        $select->limit($limit, $offset);
        $uSelect = $userModel->select()->setIntegrityCheck(false);
        $allRows = $userModel->fetchAll($uSelect);
        $oRaport = new My_Raport();
        $rows = $userModel->fetchAll($select);
        if($rows instanceof Zend_Db_Table_Rowset){
            $rows = $rows->toArray();
            foreach($rows as $key=> $row){
                $oRaportWynik = $oRaport->pobierzWynik($row['id'],$id_survey);
                if(is_array($oRaportWynik)){
                    $rows[$key]['wynik']=($oRaportWynik['podsumowanie']['wynik'] == 1)?'zdał':'nie zdał';
                }else{
                    $rows[$key]['wynik']='brak';
                }
            }
        }else{
            $rows=array();
        }


        $answer['iTotalRecords'] = $allRows->count();
        $answer['iTotalDisplayRecords'] = $filteredRows->count();
        $answer['sEcho'] = (int)$this->getRequest()->getParam('sEcho');
        $answer['aaData'] = $rows ? $rows : array();

        echo Zend_Json::encode($answer);
    }
    public function deleteoneAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $id = $this->_getParam('id', 0);
        $id_survey = $this->_getParam('id_survey', 0);
        $oSurveyElement = new Application_Model_DbTable_Surveyelements();
        $oSurveyElement->delete('id='.$id);

        $this->_redirect('survey/edit/id/'.$id_survey);
    }
}
