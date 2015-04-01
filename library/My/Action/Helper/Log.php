<?php
class My_Action_Helper_Log extends Zend_Controller_Action_Helper_Abstract
{
   protected $ip;
   protected $view;
   public function preDispatch()
    {
    	
    	$page_session_space = new Zend_Session_Namespace('page');
		
		if (isset($page_session_space->user) && isset($page_session_space->user->id_client)) {
			$loginlog = new Application_Model_DbTable_Loginlog();
			$controller=$this->getController();
    		$helper =  $controller->getHelper('Layout');
			$layout = $helper->getLayoutInstance();
			$layout->last_correct_login=$loginlog->getDateLastCorrectLogin();
			$layout->last_failed_login=$loginlog->getDateLastFailedLogin();
    		
		}
    }
   public function getip() { 
    if (isset($_SERVER)) { 
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) { 
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"]; 
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) { 
            $realip = $_SERVER["HTTP_CLIENT_IP"]; 
        } else { 
            $realip = $_SERVER["REMOTE_ADDR"]; 
        } 
     
    } else { 
        if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) { 
            $realip = getenv( 'HTTP_X_FORWARDED_FOR' ); 
        } elseif ( getenv( 'HTTP_CLIENT_IP' ) ) { 
            $realip = getenv( 'HTTP_CLIENT_IP' ); 
        } else { 
            $realip = getenv( 'REMOTE_ADDR' ); 
        } 
    } 
    $this->ip=$realip;
    return $realip;     
 }
   public function gethost(){
    $hostname = gethostbyaddr($this->ip);
    return $hostname;
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





