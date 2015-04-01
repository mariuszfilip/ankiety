<?php

/**
 * Formularz edycji uzytkownika
 * @author gustaf
 *
 */
class Application_Form_User extends Zend_Form
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


      public function setActionName($action){
          $this->actionName = $action;
          if($action === "add"){
              $this->removeElement("password");
              $this->removeElement("passwordAgain");
              $this->removeElement("status");
          }elseif($action == "edit") {
              $this->removeElement("emailAgain");
              $this->getElement("password")->setRequired(false);
              $this->getElement("passwordAgain")->setRequired(false);
              $this->getElement('submit')->setLabel('Zapisz');
          } else if($action == "config") {
              $this->removeElement("emailAgain");
              $this->removeElement("status");
              $this->removeElement("groups");
              $this->removeElement("emails_quota_id");
              $this->removeElement("emails_quota");
              $this->removeElement("password");
              $this->removeElement("passwordAgain");
              $this->removeDisplayGroup("grupy");
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
				'label' => $this->getView()->translate('user.first_name'),
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
				'label' => $this->getView()->translate('user.last_name'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(2, 50))
				),
				'class' => 'input-text'
				));

				$email = new Zend_Form_Element_Text('email', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('email'),
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
				'label' => $this->getView()->translate('user.email_again'),
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
				'label' => $this->getView()->translate('password'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(6, 50))
				),
				'class' => 'input-password'
				));

				$passwordAgain = new Zend_Form_Element_Password('passwordAgain', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('user.password_again'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(6, 50))
				),
				'class' => 'input-password'
				));


			  $status = new Zend_Form_Element_Select('status', array(
	          'decorators' => $this->elementDecorators,
	          'label' => $this->getView()->translate('status'),
	          'required' => true,
	          'class' => 'input-text'
	          ));
	          $status->addMultiOption(0, $this->getView()->translate('no.active'));
	          $status->addMultiOption(1, $this->getView()->translate('active'));


              $czy_admin = new Zend_Form_Element_Select('czy_admin', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Czy admin?',
                  'required' => true,
                  'class' => 'input-text'
              ));
              $czy_admin->addMultiOption(0, 'Nie');
              $czy_admin->addMultiOption(1, 'Tak');



	          $submit = new Zend_Form_Element_Submit('submit', array(
				'decorators' => $this->buttonDecorators,
				'label' => $this->getView()->translate('button.save.user'),
				'class' => 'input-submit'
				));
				$submit->setAttrib('id', 'submitbutton');

				$this->addElements(array(
				$firstName,
				$lastName,
				$email,
				$emailAgain,
				$password,
				$passwordAgain,
				$status,
                $czy_admin,
				));

				$this->addDisplayGroup(
				array("first_name", "last_name", "email", "emailAgain","id_client", "password", "passwordAgain",  "status","czy_admin"), 'danelogin',
				array(
				'disableLoadDefaultDecorators' => true,
				'decorators' => $this->_standardGroupDecorator,
				'legend' => $this->getView()->translate('group.form.user')
				)
				);


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

