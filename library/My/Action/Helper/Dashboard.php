<?php
class My_Action_Helper_Dashboard extends Zend_Controller_Action_Helper_Abstract
{
    protected $view;

    public function preDispatch()
    {
    	
    	$page_session_space = new Zend_Session_Namespace('page');
		
		if (isset($page_session_space->user) && isset($page_session_space->user->id_client)) {
			$dashbard = new Application_Model_DbTable_Dashboard();
			$dashbard_result = $dashbard->showDashboard();
			$controller=$this->getController();
    		$helper =  $controller->getHelper('Layout');
			$layout = $helper->getLayoutInstance();
			$layout->dashboard=$dashbard_result;
			$view = $this->getView();
			$view->dashboard = $dashbard_result;
			$controller =Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
			$action =Zend_Controller_Front::getInstance()->getRequest()->getActionName();
			$view->title_page = $view->translate($controller.'.'.$action.'.title');
			$view->description_page = $view->translate($controller.'.'.$action.'.description');
		}
    }

    public function getView()
    {
        if (null !== $this->view)
        {
            return $this->view;
        }
        $controller = $this->getActionController();
        $this->view = $controller->view;
        return $this->view;
    }
    public function getController()
    {
    	$controller = $this->getActionController();
    	return $controller;
    }
} 





