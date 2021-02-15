<?php
require_once "config.php";

class Register {
	public $mail = null;
	public $password = null;
	private $password_hash = null;
	private $mail_code = null;

	// Parse data
	function __construct() {
		$this->mail = $_POST['email'];
		$this->password = $_POST['password'];
		$this->password_hash = password_hash(
			hash_hmac("sha256", $this->password, Config\PEPER),
			PASSWORD_BCRYPT
		);
		$this->mail_code = substr(hash("sha256", $this->password_hash), 0, 10);
	}

	function send_mail() {
		mail(
			$this->mail, 
			"Verificacion de email",
			"Codigo: " . $this->mail_code
		);
	}

	function insert() {
		$conn = new mysqli(
			\Config\DB\HOST, 
			\Config\DB\USER, 
			\Config\DB\PASSWORD, 
			\Config\DB\DB
		);

		// Make the query
		$esc_mail = $conn->escape_string($this->mail);
		$esc_passh = $conn->escape_string($this->password_hash);	
		$query = "CALL create_user('$esc_mail', '$esc_passh');";
		$result = $conn->query($query);
		
		if($result)
			$this->send_mail();
		else {
			http_response_code(500);
			echo $conn->error;
		}
		$conn->close();
	}
};
