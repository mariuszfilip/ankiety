<?php

/**
 * Formularz edycji grup ACL
 * @author gustaf
 *
 */
class Application_Form_SurveyUser extends Zend_Form
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
        $this->setAttrib('id','users_survey');
        $acls = new Application_Model_DbTable_User();
        $acls_checkbox = new Zend_Form_Element_MultiCheckbox('users');
        foreach ($acls->fetchAll() as $key => $value){
            $acls_checkbox->addMultiOption($value->id, ' '.$value->first_name.' '.$value->last_name.' '.$value->email);
        }

        $submit = new Zend_Form_Element_Submit('submit', array(
            'decorators' => $this->buttonDecorators,
            'label' => 'Zapisz',
            'class' => 'input-select'
        ));
        $submit->setAttrib('id', 'submitbutton');

        $this->addElements(array(
            $acls_checkbox
        ));
        $this->addElements(array(
            //$submit
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


