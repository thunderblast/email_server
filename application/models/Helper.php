<?php 
class Application_Model_Helper {
	//Using base64
	public static function encodePassword($password) {
		return base64_encode($password);
	}

	public static function getUniqueString() {

    	$alphaNum = "abcdefghijklmnopqrstuvwqyz0123456789";
    	$returnValue ="";
		for($i = 0 ; $i < 6 ; $i++) {
			$returnValue .= $alphaNum[rand(0,35)];
		}

		$returnValue = md5($returnValue . microtime(true));
                return $returnValue;
    }
}
?>