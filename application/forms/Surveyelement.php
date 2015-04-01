<?php

/**
 * Formularz edycji/dodawania elementów ankiet
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Surveyelement extends Zend_Form
{

      private $hiddenDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => '<span>', 'class' => 'hidden')),
			'Label',
			array(array('row' => 'HtmlTag'), array()),
      );
      private $_standardDecoratorTitle = array(
			'ViewHelper'
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

      public function init()
      {
	      $this->setMethod('post');
          $this->setAttrib('id', 'element');
	      $id = new Zend_Form_Element_Hidden('id');
	      $id->addFilter('Int');

	      $type = new Zend_Form_Element_Select('type', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('select.element'),
				'required' => true,
				'class' => 'input-text'
				));
	      $type->addMultiOption(0, $this->getView()->translate('select.element'));
	      $type->addMultiOption(1, $this->getView()->translate('header'));
//	      $type->addMultiOption(2, $this->getView()->translate('question.open'));
	      $type->addMultiOption(3, $this->getView()->translate('question.close'));






	      $submit = new Zend_Form_Element_Button('button', array(
				'decorators' => $this->buttonDecorators,
				'label' => $this->getView()->translate('add.element'),
				'class' => 'input-select'
				));
	      $submit->setAttrib('id', 'submitbutton');

	      $this->addElements(array(
	      	  $id,
		      $type
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
  
  public function populate(array $values){
      $id = $values['id'];
      if($id != ''){
           $config = new Zend_Config_Ini(APPLICATION_PATH . '/../configs/config.ini', 'general');
          $survey_element = new Application_Model_DbTable_Surveyelements();
          $survey_element_list = $survey_element->getSurveyElementsByIdsurvey((int)$id);
          if($survey_element_list){
              foreach($survey_element_list as $key => $value){
                  $type = $value['type'];
                  $id = $value['id'];
                  switch ((int)$type) {
                      case 1:
                          $title = new Zend_Form_Element_Note("title$id", array(
                                    'disableLoadDefaultDecorators' => true,
                					'value' => '<div class="title"><h6>'.$this->getView()->translate('header').'</h6><a style="float:right;padding:10px;" href="'.$config->system->path.'survey/deleteone/id/'.$id.'/id_survey/'.$values['id'].'">Usuń</a></div>',
                             		'decorators' => $this->_standardDecoratorTitle
                		  ));
                          $header = new Zend_Form_Element_Text("header$id");
                          $header->setRequired(true)->setLabel($this->getView()->translate('text.header'))->setDecorators($this->elementDecorators)
                              ;
                          $target = new Zend_Form_Element_Textarea("target$id");
                          $target->setLabel('Cel')->setDecorators($this->elementDecorators)->setAttrib('CLASS','ckeditor');
                          $priority = new Zend_Form_Element_Text("priority$id");
                          $priority->setRequired(true)->setLabel("Priorytet")->setDecorators($this->elementDecorators) ;

                          $this->addElements(array($title,$header,$target,$priority));
                          $this->addDisplayGroup(array("title$id","header$id","target$id","priority$id","priority$id"), "grupa$id",
                				array(
                					'disableLoadDefaultDecorators' => true,
                					'decorators' => $this->_standardGroupDecorator,
                					'class' => 'widget'
                					)
                				);
                          break;
                      case 2://otwarte
                          $title = new Zend_Form_Element_Note("title$id", array(
                                    'disableLoadDefaultDecorators' => true,
                					'value' => '<div class="title"><h6>'.$this->getView()->translate('question.open').'</h6><a style="float:right;padding:10px;" href="'.$config->system->path.'survey/deleteone/id/'.$id.'/id_survey/'.$values['id'].'">Usuń</a></div>',
                             		'decorators' => $this->_standardDecoratorTitle
                		  ));
                		  $type_question = new Zend_Form_Element_Select("type_question$id");
                          $type_question->setRequired(true)->setLabel($this->getView()->translate('type'))->setDecorators($this->elementDecorators);
                          $type_question->setMultiOptions(array('1'=>$this->getView()->translate('question.open.one.line'),'2'=>$this->getView()->translate('question.open.many.line')));
                          $header = new Zend_Form_Element_Text("header$id");
                          $header->setRequired(true)->setLabel($this->getView()->translate('question'))->setDecorators($this->elementDecorators);
                          $target = new Zend_Form_Element_Text("target$id");
                          $target->setLabel($this->getView()->translate('explanation.optional'))->setDecorators($this->elementDecorators);
                          $priority = new Zend_Form_Element_Text("priority$id");
                          $priority->setRequired(true)->setLabel("Priorytet")->setDecorators($this->elementDecorators) ;
                          $this->addElements(array($title,$type_question,$header,$target,$priority));
                          $this->addDisplayGroup(array("title$id","type_question$id","header$id","target$id","priority$id"), "grupa$id",
                				array(
                					'disableLoadDefaultDecorators' => true,
                					'decorators' => $this->_standardGroupDecorator,
                					'class' => 'widget'
                					)
                				);
                          
                          break;
                      case 3:
                           $title = new Zend_Form_Element_Note("title$id", array(
                                    'disableLoadDefaultDecorators' => true,
                					'value' => '<div class="title"><h6>'.$this->getView()->translate('question.close').'</h6><a style="float:right;padding:10px;" href="'.$config->system->path.'survey/deleteone/id/'.$id.'/id_survey/'.$values['id'].'">Usuń</a></div>',
                             		'decorators' => $this->_standardDecoratorTitle
                		  ));
                          $type_question = new Zend_Form_Element_Select("type_question$id");
                          $type_question->setRequired(true)->setLabel($this->getView()->translate('type'))->setDecorators($this->elementDecorators);
                          $type_question->setMultiOptions(array('1'=>$this->getView()->translate('question.close.one.answer'),'2'=>$this->getView()->translate('question.close.many.answer')));
                          $header = new Zend_Form_Element_Textarea("header$id");
                          $header->setRequired(true)
                              ->setLabel($this->getView()
                              ->translate('question'))->setDecorators($this->elementDecorators)->setAttrib('CLASS','ckeditor');

                          $target = new Zend_Form_Element_Textarea("option_question$id");
                          $target->setRequired(false)->setLabel('Opcje odpowiedzi')->
                              setDecorators($this->elementDecorators)->setAttrib('COLS', '40')->setAttrib('ROWS', '4');

                          $positive_answer = new Zend_Form_Element_Textarea("positive_answer$id");
                          $positive_answer->setRequired(false)->setLabel('Odpwiedż/i prawidłowa/e')->
                              setDecorators($this->elementDecorators)->setAttrib('COLS', '40')->setAttrib('ROWS', '2');
                          $priority = new Zend_Form_Element_Text("priority$id");
                          $priority->setRequired(true)->setLabel("Priorytet")->setDecorators($this->elementDecorators) ;

                          $points = new Zend_Form_Element_Text("pionts_for_answer$id");
                          $points->setRequired(true)->setLabel("Punkty za prawidłową odpowiedż")->setDecorators($this->elementDecorators) ;

                          $this->addElements(array($title,$type_question,$header,$target,$positive_answer,$priority,$points));
                          $this->addDisplayGroup(array("title$id","type_question$id","header$id","option_question$id","positive_answer$id","priority$id","pionts_for_answer$id"), "grupa$id",
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

       return $this->setDefaults($values);
  
  }

 
    
  
}


