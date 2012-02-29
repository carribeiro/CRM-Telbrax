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
		$sql = "select id, name, razao_social_c as razao_social, cnpj_c as cnpj from accounts left join accounts_cstm on accounts.id = accounts_cstm.id_c where accounts.deleted=0 order by name";

		$data = $this->query($sql);

		foreach ($data as $obj) {
			printf('"%s";"%s";"%s";"%s"'."\r\n", $this->decode($obj->name), $this->decode($obj->razao_social), $obj->cnpj, $obj->id);
		}
		
	}

}


$import = new Import();
$import->run();

?>
