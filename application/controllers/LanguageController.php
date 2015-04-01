<?php

/**
 * Zmiana jezyka 
 *
 * @author peterjack
 */
class LanguageController extends Zend_Controller_Action
{
    
    
    public function indexAction() {
        return $this->_redirect('index/index');
    }

    public function changeAction() {
        $config = Zend_Registry::get('config');
        $localeValue = $this->_getParam('lang');
        // usuwamy stare ciasteczko 
        setcookie("language", $localeValue, time()-(3600*24*60), '/' );
        // wstawiamy nowe ciastko
        setcookie("language", $localeValue, time()+(3600*24*60),  '/' );
        $localeValue = My_Util::sanitize_file_name($localeValue);
        $locale = Zend_Registry::get('Zend_Locale');
        $locale->setLocale($localeValue); 
        $translationFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR
                . $localeValue . '.ini';
        $translate = new Zend_Translate('ini', $translationFile, $localeValue);
        Zend_Registry::set('Zend_Translate', $translate);
        return $this->_redirect('/index/index');
    }
     public function checkAction(){
    	
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
    	
        $localeValue = isset($_COOKIE['language'])?$_COOKIE['language']:false;   
        
        if (!$localeValue) {
        	$localeValue = 'pl';
        }
        
    	echo $localeValue;
    	
    }
}

?>
