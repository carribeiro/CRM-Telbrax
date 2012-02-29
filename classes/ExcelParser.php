<?php
require CLASS_PATH.'/PHPExcel-1.7.6/Classes/PHPExcel.php';
require CLASS_PATH.'/Contrato.php';
require CLASS_PATH.'/TermoContrato.php';
require CLASS_PATH.'/Equipamento.php';
require CLASS_PATH.'/RestSugarCRM.php';

class ExcelParser {
	protected $argc;
	protected $argv;
	protected $basedir;
	protected $cachedir;
	protected $excelFile;
	protected $excel;

	protected $contas;
	protected $tipoContratos;
	protected $contratos;
	protected $termosContratos;
	protected $equipamentos;
	protected $enderecoContas;

	function  __construct() {
		ini_set('register_argc_argv', 1);

		$this->basedir = dirname(__FILE__);
		$this->cachedir = $this->basedir.'/cache';

		global $argc, $argv;

		$this->argc = $argc;
		$this->argv = $argv;
		
		$this->contas = array();
		$this->tipoContratos = array();
		$this->contratos = array();
		$this->termosContratos = array();
		$this->equipamentos = array();
		$this->enderecoContas = array();

		$this->checkUsage();
	}

	private function checkUsage() {
		if ($this->argc != 2) {
		    die("Usage: php {$this->argv[0]} <excel_file>\n");
		}
	}

	private function getDate($excelDate) {
		return date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($excelDate));
	}

	protected function getWorksheetIdByName($search) {
		foreach ($this->planilhas as $id => $name) {
			if ($search == $name) {
				return $id;
			}
		}

		die(sprintf("Não encontrei a planilha '%s'", $search));
	}

	protected function readTipoContratos() {
		$this->excel->setActiveSheetIndex($this->getWorksheetIdByName('TipoContrato'));
                $worksheet = $this->excel->getActiveSheet();

		$highestRow = $worksheet->getHighestRow();
		$highestColumn = $worksheet->getHighestColumn();

		for ($line = 2; $line <= $highestRow; ++$line) {
			$tipoContrato = new TipoContrato();

			$tipoContrato->setId($worksheet->getCellByColumnAndRow(0, $line)->getCalculatedValue());
			$tipoContrato->setNome($worksheet->getCellByColumnAndRow(1, $line)->getValue());
			$tipoContrato->setProdutos($worksheet->getCellByColumnAndRow(2, $line)->getValue());
			$tipoContrato->setDescricao($worksheet->getCellByColumnAndRow(3, $line)->getValue());

			array_push($this->tipoContratos, $tipoContrato);
		}
	}

	protected function readContratos() {
		$this->excel->setActiveSheetIndex($this->getWorksheetIdByName('Contrato'));
		$worksheet = $this->excel->getActiveSheet();

		$highestRow = $worksheet->getHighestRow();
		$highestColumn = $worksheet->getHighestColumn();

		for ($line = 2; $line <= $highestRow; ++$line) {
			$contrato = new Contrato();
			
			$contrato->setNumero($worksheet->getCellByColumnAndRow(0, $line)->getValue());

			$conta = $worksheet->getCellByColumnAndRow(1, $line)->getValue();
			#$worksheet->getCellByColumnAndRow(1, $line)->setValue(str_replace('=Contas!$B', '=Contas!$D', $conta));

			$contrato->setConta($worksheet->getCellByColumnAndRow(1, $line)->getCalculatedValue());
			$contrato->setTipoContrato($worksheet->getCellByColumnAndRow(2, $line)->getCalculatedValue());
			$contrato->setDataAssinatura($this->getDate($worksheet->getCellByColumnAndRow(3, $line)->getValue()));

			$tipoContrato = $worksheet->getCellByColumnAndRow(4, $line)->getValue();
			$worksheet->getCellByColumnAndRow(4, $line)->setValue(str_replace('=StatusContrato!$A', '=StatusContrato!$B', $tipoContrato));
			$contrato->setStatus($worksheet->getCellByColumnAndRow(4, $line)->getCalculatedValue());
			
			$tipoFatura = $worksheet->getCellByColumnAndRow(5, $line)->getValue();
			$worksheet->getCellByColumnAndRow(5, $line)->setValue(str_replace('=TipoFatura!A$', '=TipoFatura!B$', $tipoFatura));
			$contrato->setTipoFatura($worksheet->getCellByColumnAndRow(5, $line)->getCalculatedValue());

			$contrato->setAtribuido($worksheet->getCellByColumnAndRow(6, $line)->getValue());

			array_push($this->contratos, $contrato);
		}
	}
	
	protected function readTermoContratos() {
		$this->excel->setActiveSheetIndex($this->getWorksheetIdByName('TermoContrato'));
		$worksheet = $this->excel->getActiveSheet();

		$highestRow = $worksheet->getHighestRow();
		$highestColumn = $worksheet->getHighestColumn();

		for ($line = 3; $line <= $highestRow; ++$line) {
			$termoContrato = new TermoContrato();

			$termoContrato->setId($line);

			$termoContrato->setContrato($worksheet->getCellByColumnAndRow(0, $line)->getCalculatedValue());
			$termoContrato->setNumeroTermo($worksheet->getCellByColumnAndRow(1, $line)->getValue());
			
			$modalidade = $worksheet->getCellByColumnAndRow(2, $line)->getValue();
			$worksheet->getCellByColumnAndRow(2, $line)->setValue(str_replace('=ModalidadeContratos!$A', '=ModalidadeContratos!$B', $modalidade));

			$termoContrato->setModalidade($worksheet->getCellByColumnAndRow(2, $line)->getCalculatedValue());
			$termoContrato->setValor($worksheet->getCellByColumnAndRow(3, $line)->getValue());
			$termoContrato->setAtivacao($worksheet->getCellByColumnAndRow(4, $line)->getValue());
			$termoContrato->setPrazo($worksheet->getCellByColumnAndRow(5, $line)->getValue());
			$termoContrato->setDataAtivacao($this->getDate($worksheet->getCellByColumnAndRow(6, $line)->getValue()));

			$endereco = $worksheet->getCellByColumnAndRow(8, $line)->getValue();
			$worksheet->getCellByColumnAndRow(8, $line)->setValue(str_replace('=Contas!$B', '=Contas!$D', $endereco));

			$termoContrato->setEnderecoCobranca($worksheet->getCellByColumnAndRow(8, $line)->getCalculatedValue());

			$produto = $worksheet->getCellByColumnAndRow(9, $line)->getValue();
			$worksheet->getCellByColumnAndRow(9, $line)->setValue(str_replace('=Produto!$B', '=Produto!$C', $produto));

			$termoContrato->setProduto($worksheet->getCellByColumnAndRow(9, $line)->getCalculatedValue());
			$termoContrato->setVelocidade($worksheet->getCellByColumnAndRow(10, $line)->getValue());
			$termoContrato->setDesignacao($worksheet->getCellByColumnAndRow(11, $line)->getValue());

			$termoContrato->setPontaA($worksheet->getCellByColumnAndRow(12, $line)->getValue());
			$termoContrato->setPontaB($worksheet->getCellByColumnAndRow(13, $line)->getValue());

			array_push($this->termosContratos, $termoContrato);
		}
	}

	
	protected function readEquipamentos() {
		$this->excel->setActiveSheetIndex($this->getWorksheetIdByName('Equipamento'));
		$worksheet = $this->excel->getActiveSheet();

		$highestRow = $worksheet->getHighestRow();
		$highestColumn = $worksheet->getHighestColumn();

		for ($line = 2; $line <= $highestRow; ++$line) {
			$equipamento = new Equipamento();

			$equipamento->setIdTermoContrato(str_replace('=TermoContrato!$B$', '', $worksheet->getCellByColumnAndRow(0, $line)->getValue()));
			$equipamento->setNumeroTermo($worksheet->getCellByColumnAndRow(0, $line)->getCalculatedValue());
			$equipamento->setModeloEquipamento($worksheet->getCellByColumnAndRow(1, $line)->getCalculatedValue());
			$equipamento->setSerial($worksheet->getCellByColumnAndRow(2, $line)->getCalculatedValue());
			$equipamento->setNumeroPatrimonio($worksheet->getCellByColumnAndRow(3, $line)->getCalculatedValue());

			array_push($this->equipamentos, $equipamento);
		}
	}

	protected function readEnderecosContas() {
		$this->excel->setActiveSheetIndex($this->getWorksheetIdByName('Endereços'));

		$worksheet = $this->excel->getActiveSheet();

		$highestRow = $worksheet->getHighestRow();
		$highestColumn = $worksheet->getHighestColumn();

		for ($line = 2; $line <= $highestRow; ++$line) {
			$enderecoConta = new EnderecoConta();

			$enderecoConta->setConta($worksheet->getCellByColumnAndRow(0, $line)->getCalculatedValue());
			$enderecoConta->setEndereco($worksheet->getCellByColumnAndRow(1, $line)->getCalculatedValue());

			array_push($this->enderecoContas, $enderecoConta);
		}
	
	}

	public function showPlanilhas() {
		printf("Planilhas:\n");
		
		for ($i = 0, $max = count($this->planilhas); $i < $max; ++$i) {
			$this->excel->setActiveSheetIndex($i);
			printf("\t%s[%s]\n", $this->planilhas[$i], $this->excel->getActiveSheet()->calculateWorksheetDimension());
		}
	}
	
	public function toComboValues($values) {
		if (!is_array($values)) $values = array($values);

		foreach ($values as $pos => $item) {
			$values[$pos] = sprintf('^%s^', $item);
		}
		return implode(',', $values);
	}

	public function initParser() {
		$this->excelFile = $this->argv[1];
		$this->excel = null;

		$this->reader = PHPExcel_IOFactory::createReaderForFile($this->excelFile);
		$this->reader->setReadDataOnly(true);

		$this->excel = $this->reader->load($this->excelFile);

		$this->planilhas = $this->reader->listWorksheetNames($this->excelFile);
	}
}
?>
