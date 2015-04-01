<?php

/**
 * Zarządzanie użytkownikami aplikacji
 * @author gustaf
 *
 */
class UserController extends Zend_Controller_Action
{
	static public $labelTable = array("id" => "Id",
    	    		       "first_name" => "Imię",
    	    		       "last_name" => "Nazwisko",
    	    		       "email" => "Email",
    	    		       "city" => "Miejscowość",
    	    		       "group" => "Grupy",
    	    		       "status" => "Status",
                           "emails_sent" => "Wysłano",
                           "emails_quota" => "Limit");

	static public $actionTable = array("edit" => "Edytuj", "delete" => "Usuń");

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		 $this->view->messages = $this->_flashMessenger->getMessages();
	}

	public function preDispatch()
	{
		$page_session_space = new Zend_Session_Namespace('page');
		if (!isset($page_session_space->user) || $page_session_space->user==null) {
	  			$this->_redirect('/auth/login');
		}
	}

	/**
	 * Default action. Redirects to list action
	 *
	 * @author Michał Piasecki <michal.piasecki@e-surf.pl>
	 * @param
	 * @return
	 */
	public function indexAction()
	{
		$this->_forward("list");
	}

	/**
	 * Creates new user element
	 *
	 * @author Michał Piasecki <michal.piasecki@e-surf.pl>
	 * @param
	 * @return bool
	 */
	public function addAction()
	{
	    if ($this->getRequest()->isXmlHttpRequest()) {
	        $this->_helper->layout()->disableLayout();
	        if ($this->getRequest()->isPost())
	        $this->_helper->viewRenderer->setNoRender(true);
	    }
	    $this->view->title = $this->view->translate('add.new.user');
	    $this->view->headTitle($this->view->title);
	    $form = new Application_Form_User();
	    $defaultValue = array('status' => "1");
	    $form->setDefaults($defaultValue);
	    $form->setActionName("add");
	    $this->view->form = $form;
	    $config = Zend_Registry::get('config');
	    $this->view->syspath = $config->system->path;
	    $user = new Application_Model_DbTable_User();

	    if ($this->getRequest()->isPost()) {
	        $result= array();
            $defaultValue = array(  'status' => $form->getValue('status') ? $form->getValue('status') : "1",
                                    'city' => $form->getValue('city') ? $form->getValue('city') : "0"
                                    );
	        $form->setDefaults($defaultValue);
	        $result = array();
	        try{
	        $formData = $this->getRequest()->getPost();
	        if ($form->isValid($formData)) {
	            if ($user->getSingleWithEmail($formData['email']) != null) {
	                $result['result']='error';
	                $result['messages'] = $this->view->translate('email.already.taken');
	            } else if ($formData['email'] != $formData['emailAgain']) {
	                $result['result']='error';
	                $result['messages'] = $this->view->translate('emails.must.match');
	            } else {
                    $password = md5($form->getValue('email')."przykladowe_ziarnko");
	                $data['first_name'] = $form->getValue('first_name');
	                $data['last_name'] = $form->getValue('last_name');
	                $data['email'] = $form->getValue('email');
                    $data['czy_admin'] = $form->getValue('czy_admin');
	                $data['password'] = md5($password);
	                $data['status'] =0;
                    $data['hash'] =  md5($form->getValue('first_name').$form->getValue('email'));
                    $user_id = $user->addUser($data);

                    if( $data['czy_admin'] == 1){
                        $config = Zend_Registry::get('config');
                        $link=$config->system->path.'confirm/active/hash/'.$data['hash'].'/';
                        $mail=new PHPMailer();
                        $mail->IsSMTP();
                        $mail->SMTPAuth = true;
                        $mail->SMTPSecure = $config->mailserver->smtpsecure;
                        $mail->Host = $config->mailserver->host;
                        $mail->Port =  $config->mailserver->port;
                        $mail->Username = $config->mailserver->username;
                        $mail->Password = $config->mailserver->password;
                        $mail->CharSet = "UTF-8";
                        $mail->FromName =$config->mail->sender;
                        $mail->From = $config->mail->email;
                        $mail->Subject ='Potwierdzenie dodania nowego użytkownika';
                        $mail->IsHTML(true);
                            $mail->Body = 'Email został wysłany po dodaniu nowego konta na stronie '.$config->mail->domain->name.'<br/>
                            Login : '.$data['email'].' <br/>
                            Hasło : '.$password.' <br/>
                            .<a href='.$link.'>Kliknij aby aktywować konto</a>';

                        $mail->AddAddress($data["email"]);
                        try {
                            $mail->send();
                        }catch (Exception $ex) {
                            echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
                            var_dump($mail);
                            exit();
                        }
                    }

	                $result['result']='success';
	                $result['messages']=$this->view->translate('user.added');
                    $this->_flashMessenger->addMessage($this->view->translate('user.added'));
                    $this->_redirect('user');
	            }
	        } else {
					$result['result']='error';
					$html='';
		        	foreach($form->getMessages() as $key => $value){
		        	  foreach($value as $message_key => $message_value){
			        	    $key = str_replace("_",".",$key);
			        	    $html.="<div class='errors'><strong>".$this->view->translate($key.'.user')."</strong>: $message_value</div>";
                          $this->_flashMessenger->addMessage("<div class='errors'><strong>".$this->view->translate($key.'.user')."</strong>: $message_value</div>");
		        	  }
		        	}
		        	$result['messages']=$html;
                    $this->_redirect('user/add');
	        }
	        }catch(Exception $e){
	    		$result['result']='failed';
	    		$result['messages']=$e;
		    }
            $this->_flashMessenger->addMessage($result['messages']);
            $this->_redirect('user/add');
	    }
	}

	/**
	 * Edits user element
	 *
	 * @author Michał Piasecki <michal.piasecki@e-surf.pl>
	 * @param int $id
	 * @return bool
	 */
	public function editAction()
	{    
	    if ($this->getRequest()->isXmlHttpRequest()) {
	        $this->_helper->layout()->disableLayout();
	        if ($this->getRequest()->isPost())
	        $this->_helper->viewRenderer->setNoRender(true);
	    }
		$this->view->title = $this->view->translate('edit.user');
		$this->view->headTitle($this->view->title);

		$form = new Application_Form_User();
		$form->setActionName("edit");
		$config = Zend_Registry::get('config');
		$this->view->syspath = $config->system->path;
		$this->view->form = $form;

		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			$defaultValue = array('status' => $form->getValue('status') ? $form->getValue('status') : "1");
			$form->setDefaults($defaultValue);
			$result = array();
            $id = (int)$this->_getParam('id', 0);
			try{
        			if ($form->isValid($formData)) {
        				if ($formData['password'] != "" && $formData['password'] != $formData['passwordAgain']) {
        				    $result['result']='error';
	                        $result['messages'] = $this->view->translate('passwords.must.match');
        				}
        				else
        				{
        					$page_session_space = new Zend_Session_Namespace('page');
        					$user_login=$page_session_space->user;

        					$data['first_name'] = $form->getValue('first_name');
        					$data['last_name'] = $form->getValue('last_name');
        					$data['email'] = $form->getValue('email');
                            $data['czy_admin'] = $form->getValue('czy_admin');


        					if($form->getValue('password') != "") {
        						$data['password'] = md5($form->getValue('password'));
        					}
        
        					$data['status'] = $form->getValue('status');
        					$users = new Application_Model_DbTable_User();
        					$users->updateUser($id, $data);
        				    $result['result']='success';
                            $this->_flashMessenger->addMessage('Użytkownik został zaktualizowany');
                            $this->_redirect('user');
        				}
        			} else {
        			    $result['result']='error';
        			    $html='';
        			    foreach($form->getMessages() as $key => $value){
        			        foreach($value as $message_key => $message_value){
        			            $key = str_replace("_",".",$key);
        			            $html.="<div class='errors'><strong>".$this->view->translate($key.'.user')."</strong>: $message_value</div>";
                                $this->_flashMessenger->addMessage("<div class='errors'><strong>".$this->view->translate($key.'.user')."</strong>: $message_value</div>");
        			        }
        			    }
        			    $result['messages']=$html;
                        $this->_redirect('user/edit/id/'.$id);
        			}
			  }catch(Exception $e){
	    		$result['result']='failed';
	    		$result['messages']=$e;
		    }
            $this->_flashMessenger->addMessage($result['messages']);
            $this->_redirect('user/edit/id/'.$id);
		} else {
			$id = $this->_getParam('id', 0);
			if ($id > 0) {
				$user = new Application_Model_DbTable_User();
				$data = $user->getUser($id);

				$form->populate($data);
			}
		}
	}

	/**
	 * Deletes user element
	 *
	 * @author Michał Piasecki <michal.piasecki@e-surf.pl>
	 * @param int $id
	 * @return bool
	 */
	public function deleteAction()
	{
	    if ($this->getRequest()->isXmlHttpRequest()) {
    	   $this->_helper->layout()->disableLayout();
		   if ($this->getRequest()->isPost())
				$this->_helper->viewRenderer->setNoRender(true);
		}
		$this->view->title = $this->view->translate('user.delete');
		$this->view->headTitle($this->view->title);
		if ($this->getRequest()->isPost()) {
			$del = $this->getRequest()->getPost('del');
			$selected = $this->getRequest()->getPost('selected');
			$users = new Application_Model_DbTable_User();
			if ($del != null) {
				$id = $this->getRequest()->getPost('id');
				$users->deleteUser($id);
			}elseif($selected){
			    $temp = explode(",", $selected);
                foreach($temp as $key => $value) {
                    if($value) {
                        $users->deleteUser($value);
                    }
                }
			}
			$this->_flashMessenger->addMessage($this->view->translate('user.deleted'));
			$this->_helper->redirector('index');
		}
		else {
			$id = $this->_getParam('id', 0);
			$users = new Application_Model_DbTable_User();
			$this->view->user = $users->getUser($id);
			$this->_flashMessenger->addMessage($this->view->translate('user.not.deleted'));
		}
	}

	/**
	 * Displays all users
	 *
	 * @author Michał Piasecki <michal.piasecki@e-surf.pl>
	 * @param
	 * @return bool
	 */
	public function listAction()
	{
        $config = Zend_Registry::get('config');
		$this->view->syspath = $config->system->path;
		$page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : '1';
		$sortField = $this->getRequest()->getParam('sort') ? $this->getRequest()->getParam('sort') : 'id';
		$sortDir = $this->getRequest()->getParam('direction') ? $this->getRequest()->getParam('direction') : 'asc';

		$where = array();
		if ($this->getRequest()->getParam('g')) {
			$where['field'] = "groupacl.id";
			$where['value'] = $this->getRequest()->getParam('g');
		}

		$user = new Application_Model_DbTable_User();

		$list = $this->view->ListData($user->fetchAllUser(array("field" => $sortField,"dir" => $sortDir, "where" => $where)), self::$labelTable, self::$actionTable, "user");

		if(isset($_POST["action"]) && $_POST["action"] == "listdata") {
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			echo $list;
			return;
		}

		$this->view->title = $this->view->translate('users');
		$this->view->headTitle($this->view->title);

		$this->view->page = $page;
		$this->view->sort = $sortField;
		$this->view->direction = $sortDir;
		$this->view->messages = $this->_flashMessenger->getMessages();

		$this->view->list = $list;
	}
	public function getListAction() {
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender();
	     
	    $columnsMap = array(
	    0 => 'id',
	    1 => 'first_name',
	    2 => 'last_name',
	    3 => 'email',
	    4 => 'status'
	    );
	     
	    $page_session_space = new Zend_Session_Namespace('page');
	    $user = $page_session_space->user;
	    $limit = $this->getRequest()->getParam('iDisplayLength', 20);
	    $offset = $this->getRequest()->getParam('iDisplayStart', 0);
	     
	    $userModel = new Application_Model_DbTable_User();
	    $select = $userModel->select()->setIntegrityCheck(false);
	    $select->distinct(true);
	    $select->from('user', array(
   			'id',
   			'first_name',
   			'last_name',
   			'email',
   			'status'
   			
   			));
   			//$select->where('user.id_client=?',$user->id_client);
   			 
   			$sortingCols = $this->getRequest()->getParam('iSortingCols', 0);
   			if($sortingCols != 0) {
   			    $sortCol = (int)$this->getRequest()->getParam('iSortCol_0');
   			    if(!array_key_exists($sortCol, $columnsMap)) {
   			        $sortCol = 3;
   			    }
   			    $sortDir = $this->getRequest()->getParam('sSortDir_0');
   			    $select->order($columnsMap[$sortCol] .' '. $sortDir);
   			     
   			}
   			 
   			$searchString = $this->getRequest()->getParam('sSearch');
   			if(!empty($searchString)) {
   			    $select->where("user.first_name like '%$searchString%' or user.last_name like '%$searchString%' or user.email like '%$searchString%'");
   			}

   			//$answer['sql'] = $select->__toString();

   			$filteredRows = $userModel->fetchAll($select);
   			 
   			$select->limit($limit, $offset);
   			$uSelect = $userModel->select()->setIntegrityCheck(false);
   			$allRows = $userModel->fetchAll($uSelect);
   			 
   			$rows = $userModel->fetchAll($select);
   			if($rows instanceof Zend_Db_Table_Rowset){
   			    $rows = $rows->toArray();
   			}else{
   			    $rows=array();
   			}
           
   			 
   			$answer['iTotalRecords'] = $allRows->count();
   			$answer['iTotalDisplayRecords'] = $filteredRows->count();
   			$answer['sEcho'] = (int)$this->getRequest()->getParam('sEcho');
   			$answer['aaData'] = $rows ? $rows : array();
   			 
   			echo Zend_Json::encode($answer);
	}


	/**
	 * Edits loged user
	 *
	 * @author Artur Woźniczak <artur@wozniczak.pl>
	 * @param
	 * @return
	 */
	public function configAction()
	{
		$this->view->title = $this->view->translate('edit.user'). ": " . Zend_Registry::get('username');
		$this->view->headTitle($this->view->title);

		$form = new Application_Form_User();
		$form->setActionName("config");
		$config = Zend_Registry::get('config');
		$this->view->syspath = $config->system->path;
		$this->view->form = $form;

		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			$defaultValue = array('status' => $form->getValue('status')?$form->getValue('status'):"1");
			$form->setDefaults($defaultValue);

			if ($form->isValid($formData)) {
					$id = (int)Zend_Registry::get('userId');
					$data['first_name'] = $form->getValue('first_name');
					$data['last_name'] = $form->getValue('last_name');
					$data['email'] = $form->getValue('email');
					$users = new Application_Model_DbTable_User();
					$users->updateUser($id, $data);

			} else {
				$form->populate($formData);
			}
		} else {
			$id = Zend_Registry::get('userId');
			if ($id > 0) {
				$user = new Application_Model_DbTable_User();
				$data = $user->getUser($id);
				$form->populate($data);
			}
		}
	}
	public function historylogAction(){
	    $config = Zend_Registry::get('config');
		$this->view->syspath = $config->system->path;
        $list = array("id" => "Id","email" => "Email","status" => "Status","date" => "Data","host" => "Host","ip" => "Ip");
    

			if($this->_request->isXmlHttpRequest()) {
			$columnsMap = array_keys($list);
		
			$limit = $this->getRequest()->getParam('iDisplayLength', 20);
			$offset = $this->getRequest()->getParam('iDisplayStart', 0);
			$sortingCols = $this->getRequest()->getParam('iSortingCols', 0);
			$sortField = 'date';
			$sortDir = 'asc';
			if($sortingCols != 0) {
				$sortCol = (int)$this->getRequest()->getParam('iSortCol_0');
				if(!array_key_exists($sortCol, $columnsMap)) {
					$sortCol = 1;
				}
				$sortDir = $this->getRequest()->getParam('sSortDir_0');
				$sortField = $columnsMap[$sortCol];
				
			}
	
			$where = array();
			$like = array();
			$searchString = $this->getRequest()->getParam('sSearch');
			if(!empty($searchString)) {
				$like = array("field" => '`date`', "value" => $searchString);
			}
			
			$log = new Application_Model_DbTable_Loginlog();
			$result = $log->fetchAllLog(array(
					"field" => $sortField,
					"dir" => $sortDir, "where" => $where, "like" => $like, "offset"=>$offset, "limit"=>$limit));
			
			foreach($result as $row) {
			     if($row['status'] == 1){
		            $row['status'] = $this->view->translate('correct.login');
		        }else{
		            $row['status'] = $this->view->translate('failed.login');
		        }
				$rows[] = $row;
			}
			
			$totalRows = $log->fetchAllLog(array(
					"field" => $sortField,
					"dir" => $sortDir ));

		    if($rows == null){
		        $rows= array();
		    }
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$answer['iTotalRecords'] = count($totalRows);
   			$answer['iTotalDisplayRecords'] = count($totalRows);
   			$answer['sEcho'] = (int)$this->getRequest()->getParam('sEcho');
   			$answer['aaData'] = $rows;
   	 
   			echo Zend_Json::encode($answer);
			return;
		}    
		
		
		$this->view->title = $this->view->translate('historylog');
		$this->view->headTitle($this->view->title);
		$this->view->messages = $this->_flashMessenger->getMessages();
		
	 
	}
	
    public function changepasswordAction(){
		$this->config = Zend_Registry::get('config');
		$this->view->title = $this->view->translate('change.password');
		$this->view->headTitle($this->view->title);
		
		$form = new Application_Form_Password();
		
		$this->view->form = $form;
		
		if ($this->getRequest()->isPost()) {
		    $formData = $this->getRequest()->getPost();
		    if ($form->isValid($formData)) {
		         
		        $this->config = Zend_Registry::get('config');
		        $user = new Application_Model_DbTable_User();
		        $password = $form->getValue('old_password');
		        $password = md5($password);
		        if ($user->checkOldPassword($password) == false) {
		            $this->view->error = $this->view->translate('old.password.is.wrong');
		        } else if ($formData['password'] != '' && $formData['password'] != $formData['passwordAgain']) {
		            $this->view->error = $this->view->translate('password.must.be.the.same');
		        } else {
		            $new_password = md5($form->getValue('password'));
		            $data['password'] = $new_password;
		            $users = new Zend_Session_Namespace('page');
		            $id= $users->user->id;
		            $client_id = $user->updateUser($id,$data);

		            $this->_flashMessenger->addMessage($this->view->translate('password.change.correct'));
		            $this->_redirect('/user/changepassword');
		        }
		    } else {
		        $form->populate($formData);
		    }
		}
    }

}
