<?php
class My_Action_Helper_Https extends Zend_Controller_Action_Helper_Abstract
{
	public function preDispatch(){
	        
	        $server = new Zend_View_Helper_ServerUrl();
	        $scheme = $server->getScheme();
	        
	        $act = new Zend_Controller_Action_Helper_Redirector();
	        $server->setScheme('https');
	        $act->gotoUrl($server->serverUrl(true));
	        
	} 
}