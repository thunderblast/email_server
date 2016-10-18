<?php
class Application_Model_RecipientsInfoMapper {
	public function __construct() {
		require_once APPLICATION_PATH . "/models/DbTable/RecipientsInfo.php";
        $this->_db_table = new Application_Model_DbTable_RecipientsInfo();
    }

    public function save(Application_Model_RecipientsInfo $recipientsInfoObject) {
        $logger = Zend_Registry::get("logger");
        $data = $recipientsInfoObject->convertObjectToArray();
        
        if( !isset($data['id']) ) {
            //$data['salt'] = $user_object->salt;
            $data['recieved_on'] = date('Y-m-d H:i:s');
            $logger->info("data to be inserted = ".var_export($data,true));
            $status = $this->_db_table->insert($data);
            $logger->info("Status = ".var_export($status,true));
        } else {
            $logger->info("data to be inserted/updated = ".var_export($data,true));
            $this->_db_table->update($data, array('user_id = ?' => $data['user_id']));
        }
    }

    public function deleteMailThroughId($ids) {
    	$logger = Zend_Registry::get("logger");
    	$data = array();
        $data['deleted_on'] = date('Y-m-d H:i:s');
        $data['is_deleted'] = 1;
        
	    $status = $this->_db_table->update($data, array('id IN (?)' => $ids));
    }
}

