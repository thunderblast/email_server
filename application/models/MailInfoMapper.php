<?php

class Application_Model_MailInfoMapper {
	public function __construct() {
        $this->_db_table = new Application_Model_DbTable_MailInfo();
    }

    public function save(Application_Model_MailInfo $mailInfoObject) {
        $logger = Zend_Registry::get("logger");
        $data = $mailInfoObject->convertObjectToArray();
        
        if( !isset($data['mail_id']) ) {
            //$data['salt'] = $user_object->salt;
            $data['sent_on'] = date('Y-m-d H:i:s');
            $logger->info("data to be inserted = ".var_export($data,true));
            $status = $this->_db_table->insert($data);
        } else {
            $logger->info("data to be updated = ".var_export($data,true));
            $status = $this->_db_table->update($data, array('mail_id = ?' => $data['user_id']));
        }
        $logger->info("Status recipients db insert = ".var_export($status,true));
        if($status > 0)
        	return $this->_db_table->getAdapter()->lastInsertId();
        else
        	return 0; 
    }

    public function deleteMailThroughId($ids) {
    	$logger = Zend_Registry::get("logger");
    	$data = array();
        $data['deleted_on'] = date('Y-m-d H:i:s');
        $data['is_deleted'] = 1;
        
	    $status = $this->_db_table->update($data, array('mail_id IN (?)' => $ids));
    }

}

