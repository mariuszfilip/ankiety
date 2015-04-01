<?php
/**
 * Formularz zmiany hasła
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Password extends Zend_Form
{

      protected $actionName = null;

      private $elementDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRight')),
			'Label',
			array(array('row' => 'HtmlTag'), array('tag' => 'div' ,'class' =>'formRow overflowHidden')),
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
          if($action == "reset") {
              $this->removeElement("old_password");
          }
      }
      public function init()
      {
	      $this->setMethod('post');

	

	       $old_password = new Zend_Form_Element_Password('old_password', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('old.password'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'class' => 'input-password'
				));
	  

	      $password = new Zend_Form_Element_Password('password', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('new.password'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				new My_Validate_Password()
				),
				'class' => 'input-password'
				));

	      $passwordAgain = new Zend_Form_Element_Password('passwordAgain', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('repeat.new.password'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				new My_Validate_Password()
				),
				'class' => 'input-password'
				));

	  

	   


	      $submit = new Zend_Form_Element_Submit('submit', array(
				'decorators' => $this->buttonDecorators,
				'label' => $this->getView()->translate('button.change.password'),
				'class' => 'blueB'
				));
	      $submit->setAttrib('id', 'submitbutton');

	      $this->addElements(array(
	      	$old_password,
		      $password,
		      $passwordAgain
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


