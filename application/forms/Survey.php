<?php

/**
 * Formularz dodawania ankiet 
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Survey extends Zend_Form
{

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

      public function init()
      {
	      $this->setMethod('post');

	      $id = new Zend_Form_Element_Hidden('id');
	      $id->addFilter('Int');

	      $name = new Zend_Form_Element_Text('name', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('name'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(2, 100))
				),
					'class' => 'input-text'
				));


          $min_points= new Zend_Form_Element_Text('min_points', array(
              'decorators' => $this->elementDecorators,
              'label' => "Ilość punktów do zdania",
              'required' => true,
              'class' => 'input-text'
          ));


          $date_start_availability = new Zend_Form_Element_Text('date_start_availability', array(
              'decorators' => $this->elementDecorators,
              'label' => 'Egzamin dostępny od:',
              'required' => true,
              'filters' => array(
                  'StringTrim'
              ),
              'validators' => array(
                  array('StringLength', false, array(2, 100))
              ),
              'class' => 'input-text'
          ));


          $date_finish_availability= new Zend_Form_Element_Text('date_finish_availability', array(
              'decorators' => $this->elementDecorators,
              'label' => 'do',
              'required' => true,
              'filters' => array(
                  'StringTrim'
              ),
              'validators' => array(
                  array('StringLength', false, array(2, 100))
              ),
              'class' => 'input-text'
          ));


          $duration= new Zend_Form_Element_Text('duration', array(
              'decorators' => $this->elementDecorators,
              'label' => 'Czas trwania (minuty)',
              'required' => true,
              'filters' => array(
                  'StringTrim'
              ),
              'class' => 'input-text'
          ));


	      $submit = new Zend_Form_Element_Submit('submit', array(
				'decorators' => $this->buttonDecorators,
				'label' => 'Zapisz',
				'class' => 'input-select'
				));
	      $submit->setAttrib('id', 'submitbutton');

	      $this->addElements(array(
	      	  $id,
		      $name,
              $min_points,
              $date_start_availability,
              $date_finish_availability,
              $duration
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


