<?php

class MailManagementController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function sendMailAction() {
    	require_once APPLICATION_PATH . "/models/MailInfo.php";
    	require_once APPLICATION_PATH . "/models/MailInfoMapper.php";
    	require_once APPLICATION_PATH . "/models/RecipientsInfo.php";
    	require_once APPLICATION_PATH . "/models/RecipientsInfoMapper.php";
    	require_once APPLICATION_PATH . "/models/UserLoginMapper.php";

    	$logger = Zend_Registry::get("logger");
    	$this->_helper->viewRenderer->setNoRender(TRUE);

    	$params = $this->_request->getParams();
    	$mailInfoObj = new Application_Model_MailInfo($params);

    	$mailInfoMapperObj = new Application_Model_MailInfoMapper();
    	try {
    		$mailId = $mailInfoMapperObj->save($mailInfoObj);
    	}catch(Exception $e) {
    		$logger->crit("Error while inserting in mail db compose mail flow." . $e->getMessage());
    	}
    	$sendStatus = 1;
    	if($mailId == 0) {
    		$sendStatus = 0;
    	}

    	$params['mail_id'] = $mailId;
    	$logger->info("Mail Id = ".var_export($mailId,true));
    	$paramsDup = $params;
    	$invalidRecipients = array();

    	$userMapper = new Application_Model_UserLoginMapper();
    	foreach ($paramsDup['recipients'] as $recipient) {
    		$logger->info("Recipients = ".var_export($recipient,true));
    		$userData = $userMapper->getUserByEmailId($recipient);
    		if($userData['user_id'] == NULL || $userData['is_deleted'] == 1) {
    			array_push($invalidRecipients , $recipient);
    		}
    		else {
    			$params['recipients'] = $userData['user_id'];
		    	$recipientInfoObj = new Application_Model_RecipientsInfo($params);
		    	$logger->info("Recipient Info obj = ".var_export($recipientInfoObj,true));

		    	$recipientsInfoMapperObj = new Application_Model_RecipientsInfoMapper();
		    	$recipientsInfoMapperObj->save($recipientInfoObj);
		    }

    	}
    	echo json_encode(array("send_status" => $sendStatus , "invalidRecipients" => $invalidRecipients));
    }

    public function viewMailAction() {
    	$logger = Zend_Registry::get("logger");
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    	require_once APPLICATION_PATH . "/models/UserViewMailInfo.php";
    	$params = $this->_request->getParams();
    	$errorMsg = "";

    	if(!isset($params['view_option']) || !isset($params['user_id'])) {
    		$errorMsg = $errorMsg + "Invalid Request";
    	}

    	$userMailInfoObj = new Application_Model_UserViewMailInfo();
    	try{
    		$result = $userMailInfoObj->viewUserMail($params['view_option'] , $params['user_id']);
    	}catch(Exception $e) {
    		$logger->crit("Exception while fetching data from db during view mail".$e->getMessage());
    	}
    	$logger->info("User mails testing 123= ".var_export($result,true));

    	echo json_encode(array("user_mails" => $result));
    }

    public function deleteRecipientMailsAction() {
    	require_once APPLICATION_PATH . "/models/RecipientsInfoMapper.php";
    	$logger = Zend_Registry::get("logger");
    	$this->_helper->viewRenderer->setNoRender(TRUE);

    	$params = $this->_request->getParams();

    	$logger->info("Deleted Ids = ".var_export($params,true));
    	$recipientsInfoMapperObj = new Application_Model_RecipientsInfoMapper();
    	try {
    		$recipientsInfoMapperObj->deleteMailThroughId($params['id']);
    		echo json_encode(array("status" => 1));
    	}catch(Exception $e) {
    		$logger->crit("Error deleting mail from recipients table " . $e->getMessage());
    		echo json_encode(array("status" => 0 , "error" => "error occured while updating data in table"));
    	}
    }

    public function deleteSentMailsAction() {
    	require_once APPLICATION_PATH . "/models/MailInfoMapper.php";
    	$logger = Zend_Registry::get("logger");
    	$this->_helper->viewRenderer->setNoRender(TRUE);

    	$params = $this->_request->getParams();

    	$logger->info("Deleted Ids = ".var_export($params,true));
    	$mailInfoMapperObj = new Application_Model_MailInfoMapper();
    	try {
    		$mailInfoMapperObj->deleteMailThroughId($params['id']);
    		echo json_encode(array("status" => 1));
    	}catch(Exception $e) {
    		$logger->crit("Error deleting mail from mail table " . $e->getMessage());
    		echo json_encode(array("status" => 0 , "error" => "error occured while updating data in table"));
    	}
    }
}

