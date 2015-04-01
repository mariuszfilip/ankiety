<?php

/**
 * Kontroler domyślny
 * @author gustaf
 *
 */
class IndexController extends Zend_Controller_Action
{

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function preDispatch()
	{
		
		$page_session_space = new Zend_Session_Namespace('page');
		if (!isset($page_session_space->user) || $page_session_space->user==null) {
	  			$this->_redirect('/auth/login');
    }
		
	

	}

	public function indexAction()
	{
        $this->_redirect('/survey/index');
			            

	}


}


