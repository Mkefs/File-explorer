<?php

require_once "config.php";

class Login {
	// User data
	public $mail = null;
	public $pass = null;
	public $vcode = null;
	// DB data
	public $verified = false;
	public $user_id = null;
	public $pass_hash = null;
	public $rootid = null;

	function __construct() {
		if(isset($_POST['email']))
			$this->mail = $_POST['email'];
		if(isset($_POST['password']))
			$this->pass = $_POST['password'];
		if(isset($_POST['code']))
			$this->vcode = $_POST['code'];
	}

	// Login methods
	private function select() {
		$ret = false;
		$conn = new mysqli(
			\Config\DB\HOST,
			\Config\DB\USER,
			\Config\DB\PASSWORD,
			\Config\DB\DB
		);
		$mail = $conn->escape_string($this->mail);
		$query = "SELECT id, password, verified, rootdir FROM users WHERE email='$mail';";
		
		$q_res = $conn->query($query);
		if($q_res->num_rows > 0){
			$ret = true;
			$result = $q_res->fetch_object();
			$this->user_id = $result->id;
			$this->pass_hash = $result->password;
			$this->verified = $result->verified;
			$this->rootid = $result->rootdir;
		}

		$q_res->free();
		$conn->close();
		return $ret;
	}

	public function login() {
		if(!$this->select()) {
			http_response_code(500);
			return;
		}
		
		$pass_pepered = hash_hmac("sha256", $this->pass, \Config\PEPER);
		$equals = password_verify($pass_pepered, $this->pass_hash);
		if($this->verified < 1 || !$equals) {
			http_response_code(500);
			return;
		}
		session_start();
		$_SESSION["id"] = $this->user_id;
		$_SESSION["email"] = $this->mail;
		$_SESSION["root"] = $this->rootid;
	}

	// Account verification methods
	private function update_v() {
		$ret = false;
		$conn = new mysqli(
			\Config\DB\HOST,
			\Config\DB\USER,
			\Config\DB\PASSWORD,
			\Config\DB\DB
		);

		$uid = $conn->escape_string($this->user_id);
		$query = "UPDATE users SET verified=1 WHERE id=$uid;";
		if($conn->query($query))
			$ret = true;

		$conn->close();
		return $ret;
	}

	public function verif() {
		if(!$this->select()) {
			http_response_code(500);
			return;
		}
		if($this->verified) {
			http_response_code(409);
			return;
		}
		$v_code = substr(hash("sha256", $this->pass_hash), 0, 10);
		if($v_code != $this->vcode) {
			http_response_code(500);
			return;
		}
		$this->update_v();
	}
};
