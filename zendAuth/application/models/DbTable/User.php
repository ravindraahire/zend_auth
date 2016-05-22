<?php
class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{
	protected $db_handler;
	protected $_name = 'user';

	public function __construct(){
		$this->db_handler = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function getUserDetailsById($id)
	{
		$select = $this->db_handler->select()
					               ->from(array('u' => 'user'),array('username' => 'uname', 'password' => 'pass'))
								   ->where('id = ?', $id);
	    return $res = $this->db_handler->fetchRow($select);
	}
}