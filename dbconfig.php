<?php
	/**
	* Database Connection
	*/
	class DbConnect {
		private $server = '13.209.89.129:59522';
		private $dbname = 'cattmunity';
		private $user = 'root';
		private $pass = 'root';

		public function connect() {
			try {
				$conn = new PDO('mysql:host=' .$this->server .';dbname=' . $this->dbname . ';charset=utf8', $this->user, $this->pass);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return $conn;
			} catch (\Exception $e) {
				echo "Database Error: " . $e->getMessage();
			}
		}
        
	}
?>