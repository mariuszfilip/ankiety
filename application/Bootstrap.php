<?php

require_once '../library/My/Util.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initIdentity() {

        Zend_Registry::set('auth', Zend_Auth::getInstance());
        $this->bootstrap('view');
        $view = $this->getResource('view');

		$page_session_space = new Zend_Session_Namespace('page');
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/../configs/config.ini', 'general');
        Zend_Registry::set('config', $config);
        if (isset($page_session_space->user) && $page_session_space->user != null) {
        	$view->identity=true;
            Zend_Registry::set('username', $page_session_space->user->email);
            Zend_Registry::set('userId', $page_session_space->user->id);
            Zend_Registry::set('id_client', $page_session_space->user->id_client);
            Zend_Registry::set('userEmailsQuotaId', $page_session_space->user->emails_quota);
            $_SESSION['ckeditor_base_url']=$config->ckeditor->base->url.$page_session_space->user->id_client.'/';
            $_SESSION['ckeditor_base_dir']=(realpath(APPLICATION_PATH.'/../'.$config->ckeditor->base->dir)).'/'.$page_session_space->user->id_client.'/';
        } else {
        	$view->identity=false;
            Zend_Registry::set('username', 'guest');
        }
        error_reporting(E_ALL | E_STRICT);
                ini_set('display_errors', 'on');
      
    }
	
    protected function _initForceSSL() {
	 $isSSL = Zend_Registry::get('config')->force_ssl;
        if($isSSL && $_SERVER['SERVER_PORT'] != '443') {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit();
        }
    }

    protected function _initDatabase() {
        $resource = $this->getPluginResource('db');
        $db = $resource->getDbAdapter();
        Zend_Registry::set("db", $db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        
    }

    protected function _initAcl() {
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');

    }
	
    protected function _initCharset(){
    	 $settings = new Application_Model_DbTable_Settings();
    	 $settings_result = $settings->getSettings();
    	 $view = $this->getResource('view');
    	 if($settings_result){
    		 $view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset='.$settings_result['charset']);
    	 }else{
    	 	$view->headMeta()->appendHttpEquiv('Content-Type','text/html; charset=UTF-8');
    	 }
    	 $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
         $viewRenderer->setView($view);
    }
    protected function _initJquery() {
        $view = $this->getResource('view');
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }

    protected function _initList() {
        $view = $this->getResource('view');
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }
	
	protected function _initHelper() {
	    //Zend_Controller_Action_HelperBroker::addHelper(new My_Action_Helper_Https());
		$excelexportHelper = new My_Helper_Excelexport();
        Zend_Controller_Action_HelperBroker::addHelper($excelexportHelper);
    	Zend_Controller_Action_HelperBroker::addHelper(new My_Action_Helper_Log());
   
	}

    protected function _initConstants() {
        $registry = Zend_Registry::getInstance();
        $registry->constants = new Zend_Config($this->getApplication()->getOption('constants'));
    }

    protected function _initSession() {
        Zend_Session::setOptions();
        Zend_Session::start();
    }

    protected function _initAutoloader() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(new My_Loader_Autoloader_PhpMailer());
    }

    /**
     * Initialize Locale and Translation
     *
     * @return void
     */
    public function _initLocale() {
        $localeValue = isset($_COOKIE['language'])?$_COOKIE['language']:false;
        if (!$localeValue) {
            $localeValue = 'pl';
        }
        $localeValue = My_Util::sanitize_file_name($localeValue);
        $locale = new Zend_Locale($localeValue);
        Zend_Registry::set('Zend_Locale', $locale);
        $translationFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR
                . $localeValue . '.ini';
        $translate = new Zend_Translate('ini', $translationFile, $localeValue);
        Zend_Registry::set('Zend_Translate', $translate);
        
        $validation_translator = new Zend_Translate(
                        array(
                            'adapter' => 'array',
                            'content' => APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR,
                            'locale' => 'pl',
                            'scan' => Zend_Translate::LOCALE_DIRECTORY
                        )
        );
        Zend_Form::setDefaultTranslator($validation_translator);
        Zend_Validate_Abstract::setDefaultTranslator($validation_translator);
    }

    /**
     * Initialize view
     *
     * @return void
     */
    public function _initViewFilter() {
        $view = $this->view;
        $view->addFilterPath('Zx/View/Filter', 'Zx_View_Filter');
        $view->setFilter('Translate');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
    }
    protected function _initNavigation()
	{
	    $this->bootstrap('layout');
	    $layout = $this->getResource('layout');
	    $view = $layout->getView();
	    $config = new Zend_Config_Xml(APPLICATION_PATH.'/../configs/navigation.xml');
	 
	    $navigation = new Zend_Navigation($config);
	    $view->navigation($navigation);
	}
    
 
	


}

