<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function _initDb(){
	    $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini','production');
    	$dbAdapter = Zend_Db::factory($config->resources->db);
		Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
		$registry = Zend_Registry::getInstance();
		$registry->dbAdapter = $dbAdapter;
    }
}

