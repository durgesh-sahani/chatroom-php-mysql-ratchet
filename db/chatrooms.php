<?php 
	
	class chatrooms
	{
		private $id;
		private $userId;
		private $msg;
		private $createdOn;
		protected $dbConn;

		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setUserId($userId) { $this->userId = $userId; }
		function getUserId() { return $this->userId; }
		function setMsg($msg) { $this->msg = $msg; }
		function getMsg() { return $this->msg; }
		function setCreatedOn($createdOn) { $this->createdOn = $createdOn; }
		function getCreatedOn() { return $this->createdOn; }

		public function __construct() {
			require_once('DbConnect.php');
			$db = new DbConnect();
			$this->dbConn = $db->connect();
		}

		public function saveChatRoom() {
			$stmt = $this->dbConn->prepare('INSERT INTO chatrooms VALUES(null, :userid, :msg, :createdOn)');
			$stmt->bindParam(':userid', $this->userId);
			$stmt->bindParam(':msg', $this->msg);
			$stmt->bindParam(':createdOn', $this->createdOn);
			
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public function getAllChatRooms() {
			$stmt = $this->dbConn->prepare("SELECT c.*, u.name FROM chatrooms c JOIN users u ON(c.userid = u.id) ORDER BY c.id DESC");
			$stmt->execute();
			$chatrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $chatrooms;
		}

	}
 ?>