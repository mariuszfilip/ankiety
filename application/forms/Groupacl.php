<?php

/**
 * Formularz edycji grup ACL
 * @author gustaf
 *
 */
class Application_Form_Groupacl extends Zend_Form
{

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

      public function init()
      {
	      $this->setMethod('post');

	      $id = new Zend_Form_Element_Hidden('id');
	      $id->addFilter('Int');

	      $name = new Zend_Form_Element_Text('name', array(
				'decorators' => $this->elementDecorators,
				'label' => 'Nazwa',
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
				'label' => 'Status',
				'required' => true,
				'class' => 'input-text'
				));
	      $status->addMultiOption(0, "nieaktywna");
	      $status->addMultiOption(1, "aktywna");

	      $description = new Zend_Form_Element_Textarea('description', array(
				'decorators' => $this->elementDecorators,
				'label' => 'Opis',
				'required' => true,
				'class' => 'input-text',
				));

	      $acls = new Application_Model_DbTable_Acl();
	      $acls_checkbox = new Zend_Form_Element_MultiCheckbox('acls');
	      foreach ($acls->fetchAll() as $key => $value){
	      	      $acls_checkbox->addMultiOption($value->id, $value->name);
	      }

	      $submit = new Zend_Form_Element_Submit('submit', array(
				'decorators' => $this->buttonDecorators,
				'label' => 'Dodaj',
				'class' => 'input-select'
				));
	      $submit->setAttrib('id', 'submitbutton');

	      $this->addElements(array(
	      	      $id,
		      $name,
		      $description,
		      $status,
		      $acls_checkbox
	      ));

	      $this->addDisplayGroup(array("id", "name", "description", "status"), 'dane',
				array(
					'disableLoadDefaultDecorators' => true,
					'decorators' => $this->_standardGroupDecorator,
					'legend' => "Dane podstawowe"
					)
				);

	      $this->addDisplayGroup(array("acls"), 'acl',
				array(
					'disableLoadDefaultDecorators' => true,
					'decorators' => $this->_standardGroupDecorator,
					'legend' => "ACL"
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


