<?php

class Application_Model_UserLoginMapper {

	public function __construct() {
        $this->_db_table = new Application_Model_DbTable_UserLogin();
    }

    public function save(Application_Model_UserLogin $userLoginObject) {
        $logger = Zend_Registry::get("logger");
        $data = $userLoginObject->convertObjectToArray();
        
        if( !isset($data['user_id']) ) {
            //$data['salt'] = $user_object->salt;
            $data['added_on'] = date('Y-m-d H:i:s');
            $data['modified_on'] = date('Y-m-d H:i:s');
            $logger->info("data to be inserted/updated = ".var_export($data,true));
            $this->_db_table->insert($data);
        } else {
            $logger->info("data to be inserted/updated = ".var_export($data,true));
            $this->_db_table->update($data, array('user_id = ?' => $data['user_id']));
        }
    }
     
    public function getUserByEmailId($emailId) {
        //use the Table Gateway to find the row that
        //the id represents
        $logger = Zend_Registry::get("logger");
        $where = $this->_db_table->getAdapter()->quoteInto('email_id = ?', $emailId);
        $result = $this->_db_table->fetchAll($where);
        $row = $result->current();
        $userObject = new Application_Model_UserLogin($row);
        $userData = $userObject->convertObjectToArray();
        $logger->info("User data = ".var_export($userData,true));
        //return the user object
        return $userObject->convertObjectToArray();
    }

    public function setAuthTokenForUser($userId , $authToken) {
        $data = array();
        $data['modified_on'] = date('Y-m-d H:i:s');
        $data['auth_token'] = $authToken;

        $this->_db_table->update($data, array('user_id = ?' => $userId));
    }

    public function checkAuthForUser($userId , $emailId , $authToken) {
        $logger = Zend_Registry::get("logger");
        $where = "user_id = '" . $userId . "' AND email_id = '" . $emailId . "' AND auth_token = '" . $authToken . "'";
        $logger->info("User Authorisation where clause".var_export($where,true));
        $result = $this->_db_table->fetchAll($where);
        $row = $result->current();
        $userObject = new Application_Model_UserLogin($row);
        $userData = $userObject->convertObjectToArray();
        $logger->info("User Authorisation data = ".var_export($userData,true));
        //return the user object
        return $userObject->convertObjectToArray();
    }
}

