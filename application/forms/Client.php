<?php

/**
 * Formularz edycji uzytkownika
 * @author gustaf
 *
 */
class Application_Form_Client extends Zend_Form
{

      protected $actionName = null;

      private $hiddenDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => '<span>', 'class' => 'hidden')),
			'Label',
			array(array('row' => 'HtmlTag'), array()),
      );

      private $elementDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRightRegister')),
			'Label',
			array(array('row' => 'HtmlTag'), array('tag' => 'div' ,'class' =>'formRow overflowHidden')),
      );

      private $_standardGroupDecorator = array(
			'FormElements',
			array('HtmlTag', array()),
			'Fieldset',
			
      );

	private $buttonDecorators = array(
              'ViewHelper',
	    array(array('data' => 'HtmlTag')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'loginControl chartWrapper')),
	);

      private $checkboxDecorators = array(
              'ViewHelper',
              'Label',
              array(array('row' => 'HtmlTag')),
              array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkbox')),

      );

      public function setActionName($action){
          $this->actionName = $action;
          if($action == "add") {
          	  $this->removeElement("id");
              $this->removeElement("account_active");
          }else if($action == "edit"){
          	 $this->removeElement("emailAgain");
              $this->getElement("password")->setRequired(false);
              $this->getElement("passwordAgain")->setRequired(false);
              $this->getElement('submit')->setLabel('Zapisz');
          } else if($action == "register") {
          	  $this->removeElement("id");
              $this->removeElement("account_active");
               $this->removeElement("passwordAgain");
              $this->getElement('submit')->setLabel('Zapisz');
          } else {
              $this->removeElement("id");
          }
      }

      public function getActionName(){
          return $this->actionName;
      }

      public function init()
      {
	      $this->setMethod('post');

	      $id = new Zend_Form_Element_Hidden('id', array(
				'decorators' => $this->hiddenDecorators));
	      $id->addFilter('Int');
	      $this->addElements(array($id));

	      $firstName = new Zend_Form_Element_Text('first_name', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('register.first_name'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(2, 50))
				),
					'class' => 'input-text'
				));

	      $lastName = new Zend_Form_Element_Text('last_name', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('register.last_name'),
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(2, 50))
				),
				'class' => 'input-text'
				));
		$company = new Zend_Form_Element_Text('company', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('register.company'),
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(2, 50))
				),
				'class' => 'input-text'
				));
		 $phone = new Zend_Form_Element_Text('phone', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('register.phone'),
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				new My_Validate_Phone()
				),
				'class' => 'input-text'
				));
				
				
	      $email = new Zend_Form_Element_Text('email', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('register.email'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				'EmailAddress'
				),
				'class' => 'input-text'
				));

	      $emailAgain = new Zend_Form_Element_Text('emailAgain', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('register.email_again'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				'EmailAddress'
				),
				'class' => 'input-text'
				));

	  

	      $password = new Zend_Form_Element_Password('password', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('register.password'),
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
				'label' => $this->getView()->translate('register.password_again'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				new My_Validate_Password()
				),
				'class' => 'input-password'
				));

	      $status = new Zend_Form_Element_Select('account_active', array(
	          'decorators' => $this->elementDecorators,
	          'label' => $this->getView()->translate('status'),
	          'required' => true,
	          'class' => 'input-text'
	          ));
	      $status->addMultiOption(0, $this->getView()->translate('no.active'));
	      $status->addMultiOption(1, $this->getView()->translate('active'));

	   


	      $submit = new Zend_Form_Element_Submit('submit', array(
				'decorators' => $this->buttonDecorators,
				'label' => $this->getView()->translate('save.register'),
				'class' => 'blueB logMeIn'
				));
	      $submit->setAttrib('id', 'submitbutton');

	      $this->addElements(array(
		      $firstName,
		      $lastName,
		      $email,
		      $emailAgain,
		      $password,
		      $passwordAgain,
		      $company,
		      $phone,
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


