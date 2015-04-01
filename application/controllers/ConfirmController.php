<?php

/**
 * Zarządzanie potwierdzeniami kliknięcia przez klienta na link w mailu
 * @author gustaf
 *
 */
class ConfirmController extends Zend_Controller_Action
{
    protected $_flashMessenger;

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function indexAction()
	{
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
	}

	public function activeAction()
	{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->setLayout('loginout');
		$hash = $this->getRequest()->getParam('hash');
		$oUser = new Application_Model_DbTable_User();

		$aUser = $oUser->getUserByHash($hash);
		if($aUser){

			$oUser->updateUser($aUser['id'],array('active_by_link'=>1,'status'=>1));
            $this->_flashMessenger->addMessage("Konto zostało aktywowane");
		}else{
            $this->_flashMessenger->addMessage("Brak informacji o koncie");
        }

        $this->_redirect('auth');
	}


	


}



