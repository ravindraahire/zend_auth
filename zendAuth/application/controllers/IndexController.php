<?php

class IndexController extends Zend_Controller_Action
{
    protected $_auth;
    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
        echo '<pre>';print_r($this->_auth->getIdentity());
        //$this->_userNamespace = new Zend_Auth_Storage_Session('userNamespace');
        //echo '<pre>';print_r($this->_userNamespace);
    }

    public function indexAction()
    {
        // action body
        if (!$this->_auth->hasIdentity()) {
            $this->_redirect('/user/index');
        } else {
            echo 'in main index';
        }
    }
}

