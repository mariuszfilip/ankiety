<?php

/**
 * Formularz edycji odbiorcow maili
 * @author gustaf
 *
 */
class Application_Form_Subscriber extends Zend_Form
{

      protected $actionName = null;
      protected $id_subscriber = null;
      protected $id_campaign = null;
      private $elementDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'formRight')),
			'Label',
			array(array('row' => 'HtmlTag'), array('tag' => 'div' ,'class' =>'formRow overflowHidden')),
      );

      private $_standardGroupDecorator = array(
			'FormElements',
			array('HtmlTag', array('class' => 'pb10')),
			'Fieldset'
      );

      private $buttonDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
			array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'buttonSubmit')),
      );

      private $checkboxDecorators = array(
              'ViewHelper',
              array(array('row' => 'HtmlTag')),
              array(array('data' => 'HtmlTag'), array('tag' => 'li', 'class' => 'checkbox')),

      );

      public function __construct($idsubsciber = null,$idcampaign = null){
	     $this->id_subscriber=$idsubsciber;
	     $this->id_campaign=$idcampaign;
	     parent::__construct($options=null);
      }
      
      public function setActionName($action){
      	      $this->actionName = $action;
      	      if($action == "edit") {
      	      		$this->removeElement('id_subscriberlist_register');
      	           // $this->getElement('submit')->setLabel('Zapisz');
      	      }
      	      elseif($action == "profile"){
      	      		$this->removeElement('status');
      	      		$this->removeElement('groups');
      	      		$this->removeElement('id_subscriberlist_register');
      	      		$this->removeDisplayGroup('grupy');
      	      		//$this->getElement('submit')->setLabel('Zapisz');
      	      }
       		 elseif($action == "register"){
      	      		$this->removeElement('status');
      	      		$this->removeElement('groups');
      	      		$this->removeElement('id_subscriberlist');
      	      		$this->removeDisplayGroup('grupy');
      	      		//$this->getElement('submit')->setLabel('Zarejestruj');
      	      }
      	      else {
      	      		  $this->removeElement('id_subscriberlist_register');
      	      	      $this->removeElement("id");
      	      	      //$this->removeDisplayGroup('polaDodatowe');
      	      }
      }

      public function getActionName(){
		return $this->actionName;
      }

      public function init()
      {
          $this->setMethod('post');
          $this->setAttrib('id','subscriber_form');
          $id = new Zend_Form_Element_Hidden('id');
          $id->addFilter('Int');
          $this->addElements(array($id));
           
          $id_client = new Zend_Form_Element_Hidden('id_client');
          $id_client->addFilter('Int');
          $this->addElements(array($id_client));

          $firstName = new Zend_Form_Element_Text('first_name', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('first.name.subscriber'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(2, 50))
				),
				'class' => 'validate[required]'
				));

				$lastName = new Zend_Form_Element_Text('last_name', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('last.name.subscriber'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				array('StringLength', false, array(2, 50))
				),
				'class' => 'input-text'
				));

				$personal = new Zend_Form_Element_Text('personal', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('personal.subscriber'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'class' => 'input-text'
				));

				$email = new Zend_Form_Element_Text('email', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('email.subscriber'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				'EmailAddress'
				),
				'class' => 'input-text'
				));

				$cities_checkbox = new Zend_Form_Element_Text('city', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('city.subscriber'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'class' => 'input-text'
				));
				
				 $phone = new Zend_Form_Element_Text('phone', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('phone'),
				'required' => true,
				'filters' => array(
				'StringTrim'
				),
				'validators' => array(
				new My_Validate_Phone()
				),
				'class' => 'input-text'
				));
				
				
				if($this->id_subscriber != null){
				    $groups_checkbox = new Zend_Form_Element_MultiCheckbox('groups');
				    //$groups_checkbox->addDecorator($this->checkboxDecorators);
				    $subscriber_list = new Application_Model_DbTable_Subscriberlistsubscriber();
				    $subscriber_list_result = $subscriber_list->getListForSubscriber((int)$this->id_subscriber);

				    foreach($subscriber_list_result as $key_list => $value_list){
				        $groups = new Application_Model_DbTable_Group();
				        $groups_result = $groups->fetchAllGroupbyList($value_list['id_subscriberlist']);
				        if($groups_result){
				            foreach ($groups_result as $key => $value){
				                $groups_checkbox->addMultiOption($value->id, $value->name);
				            }
				        }else{
				            $groups_checkbox = new Zend_Form_Element_Hidden('groups');
				        }
				    }
				     
				}else{
				    $groups_checkbox = new Zend_Form_Element_Hidden('groups');
				     
				}

				$sub = new Application_Model_DbTable_Subscriberlist();
				$sub_list = $sub->getSubscriberlistSelect();
				 
				if($sub_list){
				    $subs_list = new Zend_Form_Element_Select('id_subscriberlist', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('id.subscriberlist.subscriber'),
				'required' => true,
				'class' => 'input-text'
				));
				$subs_list->addMultiOption('', $this->getView()->translate('select.list'));
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

      		$request = Zend_Controller_Front::getInstance()->getRequest();
		if($request->getParam('id_subscriberlist',0) != 0){
		    $subs_list->setValue($request->getParam('id_subscriberlist'));
		}
				$subs_list_register = new Zend_Form_Element_Select('id_subscriberlist_register', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('id.subscriberlist.subscriber'),
				'required' => true,
				'class' => 'input-text'
				));
				$sub = new Application_Model_DbTable_Subscriberlist();
				$sub_list = $sub->getAllSubscriberlistSelect();
				$subs_list_register->addMultiOption('', $this->getView()->translate('select.list'));
				if($sub_list){
				    foreach($sub_list as $key => $value){
				        $subs_list_register->addMultiOption($value['key'], $value['value']);
				    }
				}
				 

				$status = new Zend_Form_Element_Select('status', array(
				'decorators' => $this->elementDecorators,
				'label' => 'Status',
				'required' => true,
				'class' => 'input-text'
				));
				$status->addMultiOption(0, $this->getView()->translate('no.active'));
				$status->addMultiOption(1, $this->getView()->translate('active'));


				$birthYear = new Zend_Form_Element_Text('birth', array(
				'decorators' => $this->elementDecorators,
				'label' => $this->getView()->translate('birth'),
				'required' => false,
				'readonly' => 'true',
				'filters' => array(
				    'StringTrim'),
				'class' => 'input-text'
				));

				$submit = new Zend_Form_Element_Submit('submit', array(
				'decorators' => $this->buttonDecorators,
				'label' => $this->getView()->translate('add'),
				'class' => 'button blueB'
				));
				$submit->setAttrib('id', 'submitbutton');
				if($this->id_campaign == null){
		      $additionalfields = array();
		      $field = new Application_Model_DbTable_Additionalfield();
		      $field_result = $field->fetchAllAdditionalfieldByClient();
		      if($field_result){
		          foreach($field_result as $key => $value) {
			      	      $additionalfields["additional_".$value["id"]] = new Zend_Form_Element_Text("additional_".$value["id"], array(
						'decorators' => $this->elementDecorators,
						'label' => $value["label"],
						'required' => false,
						'filters' => array(
						'StringTrim'
						),
						'class' => 'input-text'
						));
		          }
		      }
				}else{
				    $additionalfields = array();
		      $field = new Application_Model_DbTable_Additionalfield();
		      $field_result = $field->getAllAdditionalfieldByCampaign($this->id_campaign);
		      if($field_result){
		          foreach($field_result as $key => $value) {
			      	      $additionalfields["additional_".$value["id"]] = new Zend_Form_Element_Text("additional_".$value["id"], array(
						'decorators' => $this->elementDecorators,
						'label' => $value["label"],
						'required' => false,
						'filters' => array(
						'StringTrim'
						),
						'class' => 'input-text'
						));
		          }
		      }
		      	
				}

				$this->addElements(array(
				$firstName,
				$lastName,
				$personal,
				$email,
				$phone));

				$labelTable_sub = array("first_name", "last_name", "personal", "email", "birth","city","phone","status","id_subscriberlist","id_subscriberlist_register");
				$labelTable=array();
				foreach($additionalfields as $key => $value) {
				    $this->addElements(array($value));
				    $labelTable = array_merge($labelTable, array($key));
				}

				//$labelTable = array_merge($labelTable, array("city", "status","id_subscriberlist","id_subscriberlist_register"));

				$this->addElements(array(
				$cities_checkbox,
				$status,
				$birthYear,
				$subs_list,
				$subs_list_register,
				$groups_checkbox
				));

				$this->addDisplayGroup(
				$labelTable_sub, 'daneSubscriber',
				array(
				'disableLoadDefaultDecorators' => true,
				'decorators' => $this->_standardGroupDecorator,
				'legend' => $this->getView()->translate('data'),
	      	       'class' => '',
				)
				);
				if(!empty($labelTable)){
				    $this->addDisplayGroup(
				    $labelTable, 'polaDodatowe',
				    array(
				'disableLoadDefaultDecorators' => true,
				'decorators' => $this->_standardGroupDecorator,
				'legend' => $this->getView()->translate('additional.fields'),
	      	      'class' => 'toggle additionalField'
	      	      )
	      	      );
				}
				if($groups_result){
				    $this->addDisplayGroup(
				    array("groups"), 'grupy',
				    array(
					'disableLoadDefaultDecorators' => true,
					'decorators' => $this->_standardGroupDecorator,
					'legend' => $this->getView()->translate('groups'),
					'class' => 'additionalField'
					)
					);
				}


				$this->addElements(array(
				$submit
				));

      }

	public function loadDefaultDecorators()
	{
		$this->setDecorators(array(
                  'FormErrors',
                  'FormElements',
		array('HtmlTag', array('tag' => '<div>', 'class' => 'form')),
                  'Form'
                  ));
	}
}

