<?php
require_once "config.php";

class Dirs_comp {
	public $current_dir = null;
	public $user_id = null;
	public $dir = null;

	function __construct($GET) {
		session_start();
		$this->current_dir = $_SESSION["root"];
		$this->user_id = $_SESSION["id"];
		if(isset($GET["dir"]))
			$this->current_dir = $GET["dir"];

		if(isset($GET["name"]))
			$this->dir = $GET['name'];
		elseif(isset($GET["sum"]))
			$this->dir = $GET["sum"];
	}
	
	public function get_user_dir_data() {
		$conn = new mysqli(
			\Config\DB\HOST,
			\Config\DB\USER,
			\Config\DB\PASSWORD,
			\Config\DB\DB
		);
		
		$dataRes = [];
		$current = $conn->escape_string($this->current_dir);

		$query = "call get_dirs('$current', $this->user_id);";
		$query .= "call get_files('$current', $this->user_id);";
		if($conn->multi_query($query)) {
			while($conn->more_results()) {
				if($res = $conn->store_result()) {
					array_push($dataRes, $res->fetch_all(MYSQLI_ASSOC));
					$res->free();
				}
				$conn->next_result();
			}
		}
		$conn->close();
		print_r(json_encode($dataRes));
	}

	public function create_subdir() {
		$conn = new mysqli(
			\Config\DB\HOST,
			\Config\DB\USER,
			\Config\DB\PASSWORD,
			\Config\DB\DB
		);

		$current = $conn->escape_string($this->current_dir);
		$uid = $conn->escape_string($this->user_id);
		$dirname = $conn->escape_string($this->dir);

		$query = "call create_dir('$dirname', '$current', $uid);";
		echo $conn->query($query);
		
		$conn->close();
	}
}
?>
