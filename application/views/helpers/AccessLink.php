<?php
class Zend_View_Helper_AccessLink {
    public function accesslink($controller,$action){
		if($action == null)
			$action = 'index';
		   $role='user';
		$acl = Zend_Registry::get('acl');
		if($acl->isAllowed($role,$controller,$action)) {
			return true;
		}else{
			return false;
		}
		
		
	}
}
?>

