<?php

/**
 * Formularz do generowania ankiety
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Surveygenerate extends Zend_Form
{
    
    private $actionName=null;
    private $id = 0;
    
    private $hiddenDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => '<span>', 'class' => 'hidden')),
			'Label',
			array(array('row' => 'HtmlTag'), array()),
      );

    private $elementDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRight')),
			'Label',
			array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRow overflowHidden')),
      );

    private $_standardGroupDecorator = array(
			'FormElements',
			array('HtmlTag', array()),
			'Fieldset'
      );
   private $_standardDecoratorTitle = array(
			'ViewHelper'
      );
    private $buttonDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'buttonSubmit')),
			array(array('row' => 'HtmlTag'), array()),
      );
    private $checkboxDecorators = array(
              'ViewHelper',
              'Label',
              array(array('row' => 'HtmlTag')),
              array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkbox')),

      );
    public function __construct($id=0){
      	      $this->id =  $id;
              parent::__construct($options=null);
      }
    public function setActionName($action){
          $this->actionName = $action;
          if($action == "preview") {
              $this->removeElement("submit");
          }elseif($action == "add"){
          
          }
      }
    public function init()
      {
           $config = new Zend_Config_Ini(APPLICATION_PATH . '/../configs/config.ini', 'general');
	      $this->setMethod('post');
          $this->setAttrib('id', 'element');
	      $id = new Zend_Form_Element_Hidden('id');
	      $id->addFilter('Int');
          if($this->id != 0){
    
              $survey_element = new Application_Model_DbTable_Surveyelements();
              $survey_element_list = $survey_element->getSurveyElementsByIdsurvey((int)$this->id);
              
              if($survey_element_list){
                  
                  foreach($survey_element_list as $key => $value){
                      $type = $value['type'];
                      $id = $value['id'];
                      $id = (int)$id;
                      switch ((int)$type) {
                          case 1:
                              $header = new Zend_Form_Element_Note("header$id");
                              $header->setDecorators($this->_standardDecoratorTitle);
                              $header->setValue('<div class="title"><h6>'.$value['header'].'</h6></div>');
                              $target = new Zend_Form_Element_Note("target$id");
                              $target->setDecorators($this->elementDecorators);
                              $target->setValue($value['target']);
                              $this->addElements(array($header,$target));
                              $this->addDisplayGroup(array("header$id","target$id"), "grupa$id",
                    				array(
                    					'disableLoadDefaultDecorators' => true,
                    					'decorators' => $this->_standardGroupDecorator,
                    					'class' => 'widget'
                    					)
                    				);
                              break;
                          case 2:
                              $group = array();
                              $header = new Zend_Form_Element_Note("header$id");
                              $header->setDecorators($this->_standardDecoratorTitle);
                              $header->setValue('<div class="title"><h6>'.$value['header'].'</h6></div>');
                              $group[] = "header$id";
                              if($value['target'] != ''){
                                  $target = new Zend_Form_Element_Note("target$id");
                                  $target->setLabel('Wyjasnienie')->setDecorators($this->elementDecorators);
                                  $target->setValue($value['target']);
                                  $this->addElements(array($target));
                                  $group[] = "target$id";
                              }
                              if($value['type_question'] == 1){
                                  $answer = new Zend_Form_Element_Text("answer$id", array('decorators' => $this->elementDecorators,
								 'label' => 'Odpowiedz','required' => true,'filters' => array('StringTrim'),'validators' => array(
				                  array('StringLength', false, array(2, 50))),'class' => 'input-text'));
                              }elseif($value['type_question'] == 2){
                                  $answer = new Zend_Form_Element_Textarea("answer$id", array(
                                				'decorators' => $this->elementDecorators,
                                				'label' => 'Odpowiedz',
                                				'required' => true,
                                                 'rows' => '5',
                                                 'cols' => '4',
                                				'class' => 'input-text',
                                  ));
                              
                              }
                              $group[]="answer$id";
                              $this->addElements(array($header,$answer));
                              
                              $this->addDisplayGroup($group, "grupa$id",
                    				array(
                    					'disableLoadDefaultDecorators' => true,
                    					'decorators' => $this->_standardGroupDecorator,
                    					'class' => 'widget'
                    					)
                    				);
                            
                              break;
                          case 3:
                              $header = new Zend_Form_Element_Note("header$id");
                              $header->setDecorators($this->_standardDecoratorTitle);
                              $header->setValue('<div class="title"><h6>'.$value['header'].'</h6></div>');
                              if($value['type_question'] == 1){
                                  $options = explode(chr(10),$value['option_question']);
                                  $answer = new Zend_Form_Element_Radio("answer$id",array('required'=>true,'decorators' => $this->checkboxDecorators,'label' => 'Odpowiedzi','multiOptions'=>$options));
                              }elseif($value['type_question'] == 2){
                                   $options = explode(chr(10),$value['option_question']);
                                   $answer = new Zend_Form_Element_MultiCheckbox("answer$id",array('required'=>true,'decorators' => $this->checkboxDecorators,'multiOptions'=>$options));
                               }
                              $this->addElements(array($header,$answer));
                              $this->addDisplayGroup(array("header$id","answer$id"), "grupa$id",
                    				array(
                    					'disableLoadDefaultDecorators' => true,
                    					'decorators' => $this->_standardGroupDecorator,
                    					'class' => 'widget'
                    					)
                    				);
                       
                              break;
                      }
                  }
              }
          }

	      $submit = new Zend_Form_Element_Submit('submit', array(
				'decorators' => $this->buttonDecorators,
				'label' => $this->getView()->translate('save.survey'),
				'class' => 'input-select btn btn-default'
				));
	      $submit->setAttrib('id', 'submitbutton');

	      $this->addElements(array(
	      	  $id
	      ));
        
	      $this->addElements(array(
	      	      $submit
	      ));
	    



  }

    public function loadDefaultDecorators()
      {
    	  $this->setDecorators(array(
    	  'FormErrors',
    	  'FormElements',
    	  array('HtmlTag', array('tag' => 'div', 'class' => 'form')),
    	  'Form'
          ));
      }
  
}


