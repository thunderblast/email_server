<?php
class Application_Model_UserViewMailInfo {

	public function viewUserInbox($userId) {
		$logger = Zend_Registry::get("logger");
		$db = Zend_Registry::get("db");
		$db->setFetchMode(Zend_Db::FETCH_ASSOC);

		$select = $db->select()
             ->from('recipients_info as ri' , array('id' , 'recieved_on'))
             ->join('mail_info as mi',"mi.mail_id = ri.mail_id" , array('mail_id' , 'mail_subject' , 'mail_body' , 'source_id'))
             ->join('user_login as ul' ,"mi.source_id = ul.user_id" , array('user_id' , 'firstname' ,'lastname'))
             ->where('ri.destination_id = ? ', $userId)
             ->where('ri.is_deleted IS NULL OR ri.is_deleted = 0')
             ->order('recieved_on DESC');

        $logger->info("Select query : ".var_export($select,true));

        $result = $db->fetchAll($select);
        $resultMap = array();
        foreach($result as $recepMailInfo) {
			$resultMap[] = $recepMailInfo;
		}
        $logger->info("View user mail : ".var_export($resultMap,true));
        return $resultMap;
    }

    public function viewUserSentMails($userId) {
		$logger = Zend_Registry::get("logger");
		$db = Zend_Registry::get("db");
		$db->setFetchMode(Zend_Db::FETCH_ASSOC);

		$select = $db->select("*")
             ->from('recipients_info as ri',array('count(*) as count' , 'id' , 'destination_id'))
             ->join('mail_info as mi',"mi.mail_id = ri.mail_id" ,array('mail_id' , 'mail_subject' , 'mail_body' , 'sent_on'))
             ->join('user_login as ul' ,"ri.destination_id = ul.user_id" , array( 'firstname', 'lastname'))
             ->where('mi.source_id = ? ', $userId)
             ->where('mi.is_deleted IS NULL OR mi.is_deleted = 0')
             ->where('mi.is_draft IS NULL OR mi.is_draft = 0')
             ->order('ri.mail_id DESC')
             ->group('ri.mail_id');

        $logger->info("testing query : ".var_export($select->__toString(),true));
        $result = $db->fetchAll($select);
        $resultMap = array();
        $counter = 0;
        foreach($result as $recepMailInfo) {
        	$resultMap[] = $recepMailInfo;
		}
        $logger->info("total entries in groups =  ".var_export($resultMap,true));
        return $resultMap;
    }

    public function viewUserDraftMails($userId) {
		$logger = Zend_Registry::get("logger");
		$db = Zend_Registry::get("db");
		$db->setFetchMode(Zend_Db::FETCH_ASSOC);

		$select = $db->select("*")
             ->from('mail_info as mi',array('count(*) as count' , 'source_id' ,'mail_id' , 'sent_on' , "mail_subject" , "mail_body"))
             ->joinLeft('recipients_info as ri',"mi.mail_id = ri.mail_id" , array('id' , 'destination_id'))
             ->joinLeft('user_login as ul' ,"ri.destination_id = ul.user_id" , array('firstname' , 'lastname'))
             ->where('mi.source_id = ? ', $userId)
             ->where('mi.is_deleted IS NULL OR ri.is_deleted = 0')
             ->where('mi.is_draft = 1')
             ->order('mi.sent_on DESC')
             ->group('mi.mail_id');

        $logger->info("Select query : ".var_export($select->__toString(),true));

        $result = $db->fetchAll($select);
        $resultMap = array();
        foreach($result as $recepMailInfo) {
			$resultMap[] = $recepMailInfo;
		}
        $logger->info("View user mail : ".var_export($resultMap,true));
        return $resultMap;
    }

    public function viewUserTrashMails($userId) {
		$logger = Zend_Registry::get("logger");
		$db = Zend_Registry::get("db");
		$db->setFetchMode(Zend_Db::FETCH_ASSOC);

		$select = $db->select("*")
             ->from('user_login as ul',array('count(*) as count' , 'mi.deleted_on as m_del_on' ,'ul.firstname as firstname' , 'ul.lastname as lastname'))
             ->join('recipients_info as ri',"ri.destination_id = ul.user_id" , array('id'))
             ->join('mail_info as mi' ,"mi.mail_id = ri.mail_id" ,array('mail_id','mail_subject','mail_body' ,'is_draft'))
             ->where('mi.source_id = ? ', $userId)
             ->where('mi.is_deleted = 1 ')
             ->order('m_del_on DESC')
             ->group('ri.mail_id');

        $logger->info("Select query sent trash: ".var_export($select->__toString(),true));

        $result = $db->fetchAll($select);
        $resultMap = array();
        foreach($result as $recepMailInfo) {
        	$recepMailInfo['del_on'] = $recepMailInfo['m_del_on'];
			$resultMap[] = $recepMailInfo;
		}

        $select = $db->select("*")
             ->from('user_login as ul',array('count(*) as count' , 'ri.deleted_on as r_del_on' ,'ul.firstname as firstname' , 'ul.lastname as lastname' ))
             ->join('mail_info as mi',"mi.source_id = ul.user_id",array('mail_id','mail_subject','mail_body'))
             ->join('recipients_info as ri' ,"ri.mail_id = mi.mail_id" , array('id'))
             ->where('ri.destination_id = ? ', $userId)
             ->where('ri.is_deleted = 1 ')
             ->order('r_del_on DESC')
             ->group('ri.mail_id');

        $logger->info("Select query destination trash: ".var_export($select->__toString(),true));

        $result = $db->fetchAll($select);

        foreach($result as $recepMailInfo) {
        	$recepMailInfo['del_on'] = $recepMailInfo['r_del_on'];
			$resultMap[] = $recepMailInfo;
		}

		function cmpByDelOn($a, $b) {
	    	if($a['del_on'] < $b['del_on'])
	    		return 1;
	    	else
	    		return -1;
		}
		usort($resultMap, 'cmpByDelOn');
        $logger->info("View user mail : ".var_export($resultMap,true));
        return $resultMap;
    }

	public function viewUserMail($viewOption , $userId) {
		$result = array();
		$logger = Zend_Registry::get("logger");
		if($viewOption == "inbox") {
			$result = $this->viewUserInbox($userId);
			$logger->info("Result returned = ".var_export($result,true));
		}
		elseif($viewOption == "sent") {
			$result = $this->viewUserSentMails($userId);
		}
		elseif($viewOption == "draft") {
			$result = $this->viewUserDraftMails($userId);
		}
		elseif($viewOption == "trash") {
			$result = $this->viewUserTrashMails($userId);
		}
		return $result;
	}
}
?>