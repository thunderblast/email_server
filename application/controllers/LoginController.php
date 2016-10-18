<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function signInAction() {
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    	require_once APPLICATION_PATH . "/models/UserLogin.php";
    	require_once APPLICATION_PATH . "/models/UserLoginMapper.php";
    	require_once APPLICATION_PATH . "/models/Helper.php";

		$userMapper = new Application_Model_UserLoginMapper();
		$this->_helper->viewRenderer->setNoRender(TRUE);

		$params = $this->_request->getParams();

		$emailId = $params['email_id'];
		$password = $params['password'];
		$password = Application_Model_Helper::encodePassword($password);

		$status = array();
		$errorMessage = "";
		$userData = array();

		if(is_null($emailId) || is_null($password)) {
			$errorMessage = "Email Id or Password cannot be null";
		}
		else {
		    $userData = $userMapper->getUserByEmailId($emailId);
		    if($userData['user_id'] == NULL) {
		    	$errorMessage = "Email Id Not Registered";
		    }
		    else if($userData['is_deleted'] == 1) {
		    	$errorMessage = "Your account has been deleted";
		    }
		    else if($userData['password'] != $password) {
		    	$errorMessage = "Incorrect Password";
		    }
		}
		if($errorMessage == "") {
			$authToken = Application_Model_Helper::getUniqueString();
			$userMapper->setAuthTokenForUser($userData['user_id'],$authToken);
			$status = array("login_verified" => 1 , "auth_token" => $authToken , "user_id" =>$userData['user_id']);
		}
		else
			$status = array("login_verified" => 0 , "error_message" => $errorMessage);
		echo json_encode($status);
    }

    public function userAuthorisationAction() {
    	require_once APPLICATION_PATH . "/models/UserLoginMapper.php";
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    	$params = $this->_request->getParams();
    	$errorMessage = "";
    	if(!isset($params['user_id']) || !isset($params['auth_token']) || !isset($params['email_id'])) {
    		$errorMessage = "Not Authorised";
    	}
    	else {
    		$userMapper = new Application_Model_UserLoginMapper();
    		$authData = $userMapper->checkAuthForUser($params['user_id'] , $params['email_id'] , $params['auth_token']);
    		if($authData['user_id'] == NULL) {
    			$errorMessage = "Authorisation Expired or Wrong authorisation";
    		}
    	}
    	if($errorMessage == "") {
    		echo json_encode(array("auth_verified" => 1 , "user_id" => $params['user_id'] , "email_id" => $params['email_id']));
    	}
    	else {
    		echo json_encode(array("auth_verified" => 0 , "email_id" =>$params['email_id'] ,"error_message" => $errorMessage));
    	}
    }
}

