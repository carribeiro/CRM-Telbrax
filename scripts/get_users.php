#!/usr/bin/php
<?php
class Import {
	var $connection;

	function __construct() {
		$this->connection = mysql_connect('localhost', 'telbrax', 't31bra#322') or die('Error ao conectar: ' . mysql_error());
		mysql_select_db('sugarcrm', $this->connection);
	}

	private function query($sql) {
		$data = array();

		$r = mysql_query($sql, $this->connection);

		while ($obj = mysql_fetch_object($r)) {
			array_push($data, $obj);
		}

		return $data;	
	}

	private function decode($text) {
		return mb_convert_encoding($text, 'UTF-8', 'Windows-1252');		
	}

	public function run() {
		$sql = " select id, first_name, last_name from users";

		$data = $this->query($sql);

		foreach ($data as $obj) {
			printf('"%s";"%s %s"'."\r\n", $obj->id, $this->decode($obj->first_name), $this->decode($obj->last_name));
		}
		
	}

}


$import = new Import();
$import->run();

?>
