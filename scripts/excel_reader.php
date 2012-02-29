<?php
/************
#### INSTALL NOTES ###
A php_zip precisa ser instalada separadamente via "pecl install zip",
e como requisito ela pede a libpcre-dev

************/
require_once 'PHPExcel-1.7.6/Classes/PHPExcel.php';

class ExcelParser {
	private $argc;
	private $argv;
	private $basedir;
	private $cachedir;

	function  __construct() {
		ini_set('register_argc_argv', 1);

		$this->basedir = dirname(__FILE__);
		$this->cachedir = $this->basedir.'/cache';

		global $argc, $argv;

		$this->argc = $argc;
		$this->argv = $argv;

		$this->checkUsage();
	}

	private function checkUsage() {
		if ($this->argc != 2) {
		    die("Usage: php {$this->argv[0]} <excel_file>\n");
		}
	}

	private function getCache($file) {
		echo "Recuperando planilha do cache...\n";		
		$md5_file = sprintf('%s/%s.cache', $this->cachedir, md5_file($file));
		$cache = null;

		if (file_exists($md5_file)) {
			$cache = file_get_contents($md5_file);
			$cache = unserialize($cache);
			echo "Planilha recuperada do cache!\n";		
		}

		return $cache;
	}

	private function putCache($file, $data) {
		$md5_file = sprintf('%s/%s.cache', $this->cachedir, md5_file($file));

		$cache = serialize($data);
		
		if (($cache =  file_put_contents($md5_file, $cache)) !== FALSE) {
			echo "Planilha salva em cache!\n";		
		}

		return $cache;

	}

	public function run() {
		$file = $this->argv[1];
		$excel = null;
	
		$reader = new PHPExcel_Reader_Excel2007();

		//if (!($excel = $this->getCache($file))) {
		echo "Carregando planilha...\n";
		$excel = $reader->load($file);
		echo  "Planilha carregada.\n";
		//$this->putCache($file, $excel);
		//}

		$spreadsheet = $excel->getActiveSheet();


		$data = $spreadsheet->getCell('A1');

		var_dump($data->getValue());
	}
}

$parser = new ExcelParser();
$parser->run();



?>
