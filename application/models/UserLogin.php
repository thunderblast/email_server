<?php 

class Application_Model_UserLogin {
	//declare the user's attributes
    private $user_id;
    private $email_id;
    private $password;
    private $firstname;
    private $lastname;
    private $auth_token;
    private $added_on;
    private $modified_on;
    private $is_deleted;
     
    //upon construction, map the values
    //from the $user_row if available
    public function __construct($user_row = null)
    {
        if( !is_null($user_row) && $user_row instanceof Zend_Db_Table_Row ) {
            $this->user_id = $user_row->user_id;
            $this->email_id = $user_row->email_id;
            $this->password = $user_row->password;
            $this->firstname = $user_row->firstname;
            $this->auth_token = $user_row->auth_token;
            $this->added_on = $user_row->added_on;
            $this->modified_on = $user_row->modified_on;
            $this->is_deleted = $user_row->is_deleted;
        }
    }
     
    //magic function __set to set the
    //attributes of the User model
    public function __set($name, $value)
    {    
        //set the attribute with the value
        $this->$name = $value;
    }
     
    public function __get($name)
    {
        return $this->$name;
    }

    public function convertObjectToArray() {
        $data = array();
        foreach($this as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }
}

?>