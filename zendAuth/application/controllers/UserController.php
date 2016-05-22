<?php
/**
 * User controller class
 */
class UserController extends Zend_Controller_Action
{
    public $userModel;
    
    protected $_auth;

    /**
     * Initialise
     */
    public function init()
    {
        $this->userModel = new Application_Model_DbTable_User();
        $this->_auth = Zend_Auth::getInstance();

        // Use 'someNamespace' instead of 'Zend_Auth'
        //$this->_auth->setStorage(new Zend_Auth_Storage_Session('userNamespace'));
        
        $this->_userAuth = new Zend_Session_Namespace('userAuth');
        
        
    }

    /**
     * Index
     */
    public function indexAction()
    {
        if (!$this->_auth->hasIdentity()) {
            $this->_redirect('/user/login');
        }
        $identity = $this->_auth->getIdentity();echo '<pre>';print_r($identity);
        $username = (is_object($identity)) ? $identity->uname: $identity;
        $this->_userAuth = $identity;
        $this->view->assign('username', $username);
    }

    /**
     * Login
     */
    public function loginAction()
    {try {
        if ($this->_auth->hasIdentity()) {
            $this->_redirect('/user');
        }

        $db = $this->_getParam('db');
        $loginForm = new Application_Form_Login();
        if ($this->getRequest()->getPost()) {
            if ($loginForm->isValid($this->getRequest()->getPost())) {
                $username = $this->getRequest()->getPost('username');
                $password = $this->getRequest()->getPost('password');

                $dbAdapter = new Zend_Db_Adapter_Pdo_Mysql(array('dbname' => 'auth', 'username' => 'root', 'password' => ''));
                /**
                 * Set configuration values in constructor
                 */
                /*$authAdapter = new Zend_Auth_Adapter_DbTable(
                    NULL,//instance of Zend_Db_Adapter_Abstract or NULL ---- by default takes getDefaultAdapter
                    'user',
                    'uname',
                    'pass',
                    'MD5(?)'    
                );*/
                
                //$authAdapter = new Application_Form_MyAdapter($username, $password);
                
                

                /**
                 * Set adapter configuration with setter methods
                 */
                $authAdapter = new Zend_Auth_Adapter_DbTable();                
                //$authAdapter->setTableName('user');
                $authAdapter->setIdentityColumn('uname');//column contains unique value
                $authAdapter->setCredentialColumn('pass');//column contains credentials
                $authAdapter->setCredentialTreatment('MD5(?)');// hash method
                $select = $authAdapter->getDbSelect();
                $select->where('status = "active"');
                $authAdapter->setAmbiguityIdentity(true);
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential($password);
 
                //Authenticate with Zend_Auth
                //$result = $this->_auth->authenticate($authAdapter);//$result instance of Zend_Auth_Result

                //Authenticate with adapter
                $result = $authAdapter->authenticate();
                
                $this->getCodesAndMessages($result);
                

                if ($result->isValid()) {
                    $storage = $this->_auth->getStorage();//implements Zend_Auth_Storage_Interface
                    /*$storage->write($authAdapter->getResultRowObject(array(
                        'uname',
                        'real_name'
                    )));*/

                    $storage->write($authAdapter->getResultRowObject(
                        null,
                        'pass'
                    ));

                    $this->_redirect('/user/index');
                }
            } else {
                echo '<pre>';print_r($loginForm->getMessages());
            }
        }

        $this->view->loginForm = $loginForm;
    }  catch (Exception $e) {
        echo $e->getMessage();
    }
    }

    public function getCodesAndMessages($result)
    {
        switch ($result->getCode()) {
 
        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND://code = -1
            /** do stuff for nonexistent identity **/
            echo 'identity not found';
            echo '<pre>';print_r($result->getMessages());
            break;

        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID://code = -3
            /** do stuff for invalid credential **/
            echo 'wrong credential';
            echo '<pre>';print_r($result->getMessages());
            break;

        case Zend_Auth_Result::SUCCESS://code = 1
            /** do stuff for successful authentication **/
            echo 'in success';
            echo '<pre>';print_r($result->getMessages());
            break;

        case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS://code = -3
            echo 'ambiguous identity';
            echo '<pre>';print_r($result->getMessages());
            break;
        
        default:
            /** do stuff for other failure **/
            echo '<pre>';print_r($result->getMessages());
            break;
    }
    }

    /**
     * Logout
     */
    public function logoutAction()
    {echo '<pre>';print_r($_SESSION);
        if($this->_auth->hasIdentity()) {
            echo '<pre>identity = ';print_r($this->_auth);echo '</pre>';
            $this->_auth->clearIdentity();
            echo 'auth identity get cleared....';
        }
        //Zend_Session::destroy();
        /*$ns = 'userNamespace';
        $namespace = new \Zend_Session_Namespace( $ns );
        $namespace->unsetAll();*/
    }
    
    public function digestLoginAction()
    {
        $realm = 'test';
        $username = 'test';
        $password = 'test';
        $adapter = new Zend_Auth_Adapter_Digest(APPLICATION_PATH.'/configs/digest.config',
                                        $realm,
                                        $username,
                                        $password);
 
        $result = $adapter->authenticate();

        $identity = $result->getIdentity();

        print_r($identity);
        //print_r($result->getMessages());
        if ($result->isValid()) {
            echo 'valid';
        } else {
            echo 'invalid';
        }
    }

  public function httpLoginAction()
  {
    $auth = Zend_Auth::getInstance();
    if ($auth->hasIdentity()) {
      //$this->_redirect('admin/index');
        echo 'has identity';
    }

    $config = array(
      'accept_schemes' => 'basic',
      'realm' => 'My Site Admin',
    );

    $authAdapter = new Zend_Auth_Adapter_Http($config);
    //echo '<pre>';print_r($authAdapter);
    $basicResolver = new Zend_Auth_Adapter_Http_Resolver_File();
    $basicResolver->setFile(APPLICATION_PATH . '/configs/basicPasswd.txt');
    $authAdapter->setBasicResolver($basicResolver);
    
    assert($this->_request instanceof Zend_Controller_Request_Http);
    assert($this->_response instanceof Zend_Controller_Response_Http);

    $authAdapter->setRequest($this->_request);
    $authAdapter->setResponse($this->_response);

    $result = $auth->authenticate($authAdapter);
    if ($result->isValid()) {
      //$this->_redirect('admin/index');
        $this->getResponse()->setBody('valid');
    } else {
      //$this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender();
      $this->getResponse()->setBody('Not authorized!');
    }
  }
  
  public function ldapLoginAction()
  {try{
          $username = 'test';
    $password = 'test';
     
    $auth = Zend_Auth::getInstance();
     
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini',
                                  'production');
    $log_path = $config->ldap->log_path;
    $options = $config->ldap->toArray();
    unset($options['log_path']);
     
    $adapter = new Zend_Auth_Adapter_Ldap($options, $username,
                                          $password);
     
    $result = $auth->authenticate($adapter);
     
    if ($log_path) {
        $messages = $result->getMessages();
     
        $logger = new Zend_Log();
        $logger->addWriter(new Zend_Log_Writer_Stream($log_path));
        $filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
        $logger->addFilter($filter);
     
        foreach ($messages as $i => $message) {
            if ($i-- > 1) { // $messages[2] and up are log messages
                $message = str_replace("\n", "\n  ", $message);
                $logger->log("Ldap: $i: $message", Zend_Log::DEBUG);
            }
        }
    }

  } catch(Exception $e){
      echo $e->getMessage();
  }
  }
  
  public function testValidatorAction()
  {
      $testValidatorForm = new Application_Form_TestValidatorForm();
      $postData = $this->getRequest()->getPost();
      echo '<pre>';print_r($postData);
      if ($postData && $testValidatorForm->isValid($postData)) {
          
      }
      $userModel = new Application_Model_DbTable_User();
      
      $this->view->form = $testValidatorForm;
  }
}