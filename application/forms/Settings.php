<?php

/**
 * Formularz edycji ustawien
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Settings extends Zend_Form
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
		  $id = new Zend_Form_Element_Hidden('id', array(
				'decorators' => $this->hiddenDecorators));
	      $id->addFilter('Int');
	      $this->addElements(array($id));
	      
	      $listItemCountPerPage = new Zend_Form_Element_Text('item_count_per_page', array(
				'decorators' => $this->elementDecorators,
				'label' => 'Ilość wyników na stronę',
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
					'class' => 'input-text'
				));

	      $listPageRange = new Zend_Form_Element_Text('page_range', array(
				'decorators' => $this->elementDecorators,
				'label' => 'Ilość elementów w pager',
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
					'class' => 'input-text'
				));

	    
          $birthDateFormat = new Zend_Form_Element_Select('birth_date_format', array(
              'decorators' => $this->elementDecorators,
              'label' => 'Format daty urodzenia',
              'required' => true
          ));
          $birthDateFormat->addMultiOptions(array(
              'yy-mm-dd' => 'rrrr-mm-dd',
              'dd-mm-yy' => 'dd-mm-rrrr',
              'yy-mm' => 'rrrr-mm',
              'mm-yy' => 'mm-rrrr',
              'yy' => 'rrrr'
          ));
          $charset = new Zend_Form_Element_Select('charset', array(
              'decorators' => $this->elementDecorators,
              'label' => 'Kodowanie znaków',
              'required' => true
          ));
          $array_encoding = array(
          'utf-8'=>'Polskie znaki',
          'iso-8859-1'=>'Znaki zachodnioeuropejskie',
          'iso-8859-2'=>'Znaki środkowo i wschodnioeuropejskie',
          'iso-8859-3'=>'Znaki południowoeuropejskie',
          'iso-8859-4'=>'Znaki północnoeuropejskie',
          'iso-8859-5'=> 'Znaki cyryliczne',
          'iso-8859-6'=> 'Znaki arabskie',
          'iso-8859-7' =>'Znaki greckie',
          'iso-8859-8' => 'Znaki hebrajskie',
          'iso-8859-9' => 'Znaki Latin-5, bądź tureckie',
          'iso-8859-10'=> 'Znaki nordyckie lub skandynawskie',
          'iso-8859-11' =>'Znaki tajskie',
          'iso-8859-13' =>'Znaki kręgu bałtyckiego',
          'iso-8859-14' => 'Znaki celtyckie',
          'iso-8859-16' =>'Południowo-wschodnioeuropejskie',
          'iso-2022-jp' => 'Znaki japońskie');
          $charset->addMultiOptions($array_encoding);

          $confirmEmail = new Zend_Form_Element_Text('confirm_email', array(
              'decorators' => $this->elementDecorators,
              'label' => 'Adres Email z potwierdzeniami',
              'required' => true,
              'filters' => array(
                  'StringTrim'
              ),
              'validators' => array(
                  'EmailAddress'
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
	      	      $listItemCountPerPage,
	      	      $listPageRange,
	      	      $birthDateFormat,
	      	      $charset,
	      	      $confirmEmail,
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


