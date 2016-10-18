<?php
class Application_Model_RecipientsInfo {
	private $id;
	private $mail_id;
	private $destination_id;
	private $recieved_on;
	private $is_read;
	private $is_deleted;
	private $deleted_on;

	public function __construct($recipient_row = null) {
        if( !is_null($recipient_row) && is_array($recipient_row)) {
            $this->id = !empty($recipient_row['id']) ? $recipient_row['id'] : null;
            $this->mail_id = !empty($recipient_row['mail_id']) ? $recipient_row['mail_id'] : null;
            $this->destination_id = !empty($recipient_row['recipients']) ? $recipient_row['recipients'] : null;
            $this->recieved_on = !empty($recipient_row['recieved_on']) ? $recipient_row['recieved_on'] : null;
            $this->is_read = !empty($recipient_row['is_read']) ? $recipient_row['is_read'] : null;
            $this->deleted_on = !empty($recipient_row['deleted_on']) ? $recipient_row['deleted_on'] : null;
            $this->is_deleted = !empty($recipient_row['is_deleted']) ? $recipient_row['is_deleted'] : null;
        }
    }

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