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

	public function run() {
		$path_contratos = "/mnt/administrativo";

		$files = scandir($path_contratos);

		$files = array_filter($files, function ($item) {
						return preg_match('/^contrato /i', $item);
						});

		$contratos = array();

		foreach ($files as $i => $item) {
				array_push($contratos, preg_replace('/^contrato /i', '', $item));
		};

		$sql = "select id,name from accounts where deleted=0";

		$data = $this->query($sql);

		$accounts = array();

		foreach ($data as $obj) {
			//echo $obj->name," is Windows-1252: ", mb_check_encoding($obj->name,'Windows-1252') ? 'sim':'não',"\n";
			//echo $obj->name," > ", mb_convert_encoding($obj->name, 'UTF-8', 'Windows-1252'), "\n";
			array_push($accounts, mb_convert_encoding($obj->name, 'UTF-8', 'Windows-1252'));
		}
		
		natcasesort($accounts);

		echo implode("\n",$contratos);
	}

}


$import = new Import();
$import->run();

?>
