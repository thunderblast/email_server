<?php

class Application_Model_MailInfo {
	private $mail_id;
	private $source_id;
	private $parent_id;
	private $mail_subject;
	private $mail_body;
	private $sent_on;
	private $is_deleted;
	private $deleted_on;
	private $is_draft;
	private $is_reply;
	private $is_forward;

	public function __construct($mail_row = null) {
        if( !is_null($mail_row) && is_array($mail_row)) {
            $this->source_id = !empty($mail_row['user_id']) ? $mail_row['user_id'] : null;
            $this->mail_id = !empty($mail_row['mail_id']) ? $mail_row['mail_id'] : null;
            $this->parent_id = !empty($mail_row['parent_id']) ? $mail_row['parent_id'] : null;
            $this->mail_subject = !empty($mail_row['subject']) ? $mail_row['subject'] : null;
            $this->mail_body = !empty($mail_row['mail_body']) ? $mail_row['mail_body'] : null;
            $this->sent_on = !empty($mail_row['sent_on']) ? $mail_row['sent_on'] : null;
            $this->is_deleted = !empty($mail_row['is_deleted']) ? $mail_row['is_deleted'] : null;
            $this->deleted_on = !empty($mail_row['deleted_on']) ? $mail_row['deleted_on'] : null;
            $this->is_reply = !empty($mail_row['is_reply']) ? $mail_row['is_reply'] : null;
            $this->is_draft = !empty($mail_row['is_draft']) ? $mail_row['is_draft'] : null;
            $this->is_forward = !empty($mail_row['is_forward']) ? $mail_row['is_forward'] : null;
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