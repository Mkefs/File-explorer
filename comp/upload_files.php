<?php
class Upload_files {
	public $files = [];
	public $errors = [];
	public $current_dir;
	public $current_user;

	function __construct() {
		$FILES = $_FILES["files"];
		$fcount = count($FILES["error"]);
		
		for($i = 0; $i < $fcount; $i++) {
			$date = new DateTime();
			// Check the files upload
			array_push($this->errors, $FILES["error"][$i]);
			if($FILES["error"][$i] != 0)
				continue;

			array_push($this->files, [
				"name" => $FILES["name"][$i],
				"type" => $FILES["type"][$i],
				"tmp_name" => $FILES["tmp_name"][$i],
				"size" => $FILES["size"][$i],
				"rname" => md5($date->getTimestamp() . "-" . $FILES["name"][$i])
			]);
		}
		
		if(isset($_GET["dir"]))
			$this->current_dir = $_GET["dir"];
		else
			$this->current_dir = $_SESSION["root"];
		$this->current_user = $_SESSION["id"];
	}
	
	public function upload_files() {
		require "config.php";
		$conn = new mysqli(
			\Config\DB\HOST,
			\Config\DB\USER,
			\Config\DB\PASSWORD,
			\Config\DB\DB
		);
		$fcount = count($this->files);

		$err = false;
		$conn->begin_transaction();
		for($i = 0; $i < $fcount; $i++) {
			$state = $conn->prepare("call upload_file(?, ?, ? ,?, ?)");
			$state->bind_param("ssisi",
				$this->files[$i]["name"],
				$this->files[$i]["rname"],
				$this->files[$i]["size"],
				$this->current_dir,
				$this->current_user
			);
			if(!$state->execute()) {
				$err = true;
				print_r($state->error);
				break;
			}
			$rootdir = dirname(__DIR__);
			move_uploaded_file(
				$this->files[$i]["tmp_name"],
				"$rootdir/uploaded/" . $this->files[$i]["rname"]
			);
		}
		if($err){
			$conn->rollback();
		} else {
			echo json_encode($this->errors);
			$conn->commit();
		}
		$conn->close();
	}
}
