<?php
class Zend_View_Helper_List {

	private $config;

	public function __construct($data) {
		$config = Zend_Registry::get('config');
		var_dump($config);
	}
}
?>

