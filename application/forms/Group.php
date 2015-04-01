<?php

/**
 * Formularz edycji grup odbiorcow
 * @author gustaf
 *
 */
class Application_Form_Group extends Zend_Form
{
     protected $actionName = null;
      private $elementDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRight')),
			'Label',
			array(array('row' => 'HtmlTag'), array('tag' => 'div' ,'class' =>'formRow overflowHidden')),
      );
      
       private $elementDecoratorsToogle = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRight')),
			'Label',
			array(array('row' => 'HtmlTag'), array('tag' => 'div' ,'class' =>'formRow overflowHidden','id'=>'time_select')),
      );

      private $_standardGroupDecorator = array(
			'FormElements',
			array('HtmlTag', array()),
			'Fieldset'
      );

      private $buttonDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'buttonSubmit'))
      );

      private $checkboxDecorators = array(
              'ViewHelper',
              'Label',
              array(array('row' => 'HtmlTag')),
              array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkbox')),

      );
      public function setActionName($action){
          $this->actionName = $action;
          if($action == "edit") {
              $this->removeElement("choose");
              $this->removeElement("time_add");
          }
      }

      public function init()
      {
	      $this->setMethod('post');

	      $id = new Zend_Form_Element_Hidden('id');
	      $id->addFilter('Int');

	      $name = new Zend_Form_Element_Text('name', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('name.group'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(2, 50))
				),
					'class' => 'input-text'
				));

	      $status = new Zend_Form_Element_Select('status', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('status'),
				'required' => true,
				'class' => 'input-text'
				));
	      $status->addMultiOption(0, $this->getView()->translate('no.active'));
	      $status->addMultiOption(1, $this->getView()->translate('active'));
            
	      
            
	     $time_add = new Zend_Form_Element_Checkbox('time_add', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('time.add.group'),
				'required' => true,
				'class' => 'input-text',
	             'id'=>'time_add'
				)); 
	      
	      $description = new Zend_Form_Element_Textarea('description', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('description.group'),
				'required' => true,
				'class' => 'input-text',
				));

		  $subs_list = new Zend_Form_Element_Select('id_subscriberlist', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('id.subscriberlist.group'),
				'required' => true,
				'class' => 'input-text'
				));
				$sub = new Application_Model_DbTable_Subscriberlist();
				$sub_list = $sub->getSubscriberlistSelect();
	      
		   if($sub_list){
		       $subs_list = new Zend_Form_Element_Select('id_subscriberlist', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('id.subscriberlist.group'),
				'required' => true,
				'class' => 'input-text'
				));
				$subs_list->addMultiOption('', "Wybierz liste");
				foreach($sub_list as $key => $value){
	      	$subs_list->addMultiOption($value['key'], $value['value']);
				}
		   }else{
		       $subs_list = new Zend_Form_Element_Button('id_subscriberlist', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('no.active.subscriberlist'),
				'required' => true,
				'id' => 'add_list',
				'class' => 'input-text'
				));
					
		   }
		 $choose_time = new My_Form_Element_Choosetime('choose',array(
          'decorators' => $this->elementDecoratorsToogle,
          'label' => $this->getView()->translate('choose.group')));
	      
	      $submit = new Zend_Form_Element_Submit('submit', array(
				'decorators' => $this->buttonDecorators,
				'label' => $this->getView()->translate('add'),
				'class' => 'blueB'
				));
	      $submit->setAttrib('id', 'submitbutton');

	      $this->addElements(array(
	      	      $id,
		      $name,
		      $description,
		       $subs_list,
		       $time_add,
		       $choose_time,
		       $status
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
    		array('HtmlTag', array('tag' => '<fieldset>', 'class' => 'form')),
                      'Form'
                      ));
    	}
}


