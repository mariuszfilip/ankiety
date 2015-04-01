<?php

/**
 * Formularz edycji uprawnien uzytkownikow
 * @author gustaf
 *
 */
class Application_Form_Auth extends Zend_Form
{
	private $elementDecorators = array(
	            'FormErrors',
              'ViewHelper',
    	array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'loginInput')),
              'Label',
    	array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRow chartWrapper')),
	);

	private $buttonDecorators = array(
              'ViewHelper',
	    array(array('data' => 'HtmlTag')),
	    array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'loginControl chartWrapper')),
	);

	private $checkboxDecorators = array(
	  'ViewHelper',
	  'Label',
 	   array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'loginControl chartWrapper')),
	   array(array('data' => 'HtmlTag'), array('class' => 'rememberMe chartWrapper')), 
	 );
	
	public function init()
	{
		$this->setMethod('post');

		$username = new Zend_Form_Element_Text('email', array(
                  'decorators' => $this->elementDecorators,
                  'label' => $this->getView()->translate('email'),
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50))
                      ),
                  'class' => 'validate[required]'
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
                  'class' => 'validate[required]'
                  ));

        $rememberMe = new Zend_Form_Element_Checkbox('rememberMe', array(
                   'decorators' => $this->checkboxDecorators,
                   'label' => $this->getView()->translate('remember.me'),
                   'required' => true,
                   'class' => 'input-checkbox'
                   ));
        
        $rememberMe->getDecorator('label')->setOption('placement', 'APPEND');

        $submit = new Zend_Form_Element_Submit('login', array(
                  'decorators' => $this->buttonDecorators,
                  'label' => $this->getView()->translate('button.login'),
                  'class' => 'blueB logMeIn'
                  ));

                  $this->addElements(array(
                  $username,
                  $password,
                  $rememberMe,
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
?>
