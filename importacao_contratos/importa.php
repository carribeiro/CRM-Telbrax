<?php
/************
#### INSTALL NOTES ###
A php_zip precisa ser instalada separadamente via "pecl install zip",
e como requisito ela pede a libpcre-dev

************/

include 'config.php';
require CLASS_PATH.'/ExcelParser.php';

class ImportaContratos extends ExcelParser {
	private $indexContratos;
	private $indexTiposContratos;
	private $indexTermosContratos;
	private $indexAccounts;
	private $logFile;

	function  __construct() {
		parent::__construct();

		$this->indexContratos = array();
		$this->indexTiposContratos = array();
		$this->indexTermosContratos = array();
		$this->indexAccounts = array();

		$this->logFile = 'output_import.log';
	}

	public function log($text, $output = true) {
		$output && printf("%s\n", $text);
		file_put_contents($this->logFile, sprintf("%s %s\n", date('[d-m-Y H:i:s]', time()),$text), FILE_APPEND);
	}

	public function setIndexContratos($index, $value) {
		$this->indexContratos[$index] = $value;
	}

	public function getIndexContratos($index) {
		return $this->indexContratos[$index];
	}

	public function setIndexTiposContratos($index, $value) {
		$this->indexTiposContratos[$index] = $value;
	}

	public function getIndexTiposContratos($index) {
		return $this->indexTiposContratos[$index];
	}

	public function setIndexTermosContratos($index, $value) {
		$this->indexTermosContratos[$index] = $value;
	}

	public function getIndexTermosContratos($index) {
		return $this->indexTermosContratos[$index];
	}

	public function getIndexAccounts($name) {
		if (!isset($this->indexAccounts[$name])) {
			$sql = sprintf("accounts.name like '%s'", $name);

			$result = $this->rest->getEntryList('Accounts', $sql);

			if ($result->result_count != 1) {
				die(sprintf("Encontrei %s resultados na busca: %s", $result->result_count, $sql));
			}

			$entry = current($result->entry_list);
			
			$this->indexAccounts[$name] = $entry->name_value_list->id->value;
		}

		return $this->indexAccounts[$name];
	}


	public function initRest() {
		$this->log("Iniciando importação!");
		$this->log("Fazendo login no SugarCRM...");
		
		$this->rest = new RestSugarCRM('http://192.168.134.150/sugarcrm');
		#$this->rest = new RestSugarCRM('http://172.17.7.34');
		  
		$this->rest->doLogin('vinicius', 'Spark013');

		$this->log("Login bem sucedido!");
	}

	public function importToSugarCRM() {
		$this->initRest();

		$this->log("Iniciando importação de Tipos de Contrato");

		foreach ($this->tipoContratos as $i => $tipoContrato) {
			$this->log(sprintf("\tAdicionando tipo de contrato '%s'...", $tipoContrato->getNome()));

			$data = array(array('name' => 'document_name', 'value' => $tipoContrato->getNome()));

			$entry = $this->rest->setEntry('TLBRX_TipoContrato', $data);
			
			$this->tipoContratos[$i]->setId($entry->id);

			$this->setIndexTiposContratos($tipoContrato->getNome(), $entry->id);
		}

		$this->log("Iniciando importação de Contratos!");

		foreach ($this->contratos as $i => $contrato) {
			$this->log(sprintf("\tAdicionando contrato '%s'...", $contrato->getNumero()));

			$idTipoContrato = $this->getIndexTiposContratos($contrato->getTipoContrato());

			$data = array(
				array('name' => 'name',					  	   	   'value' => $contrato->getNumero()),
				array('name' => 'date_signing', 			  		   'value' => $contrato->getDataAssinatura()),
				array('name' => 'status', 				  		   'value' => $contrato->getStatus()),
				array('name' => 'invoice_type',						   'value' => $contrato->getTipoFatura()),
				array('name' => 'tlbrx_contrato_accountsaccounts_ida', 	  		   'value' => $this->getIndexAccounts($contrato->getConta())),
				array('name' => 'tlbrx_contrato_tlbrx_tipocontratotlbrx_tipocontrato_ida', 'value' => $idTipoContrato)
			);
			
			$entry = $this->rest->setEntry('TLBRX_Contrato', $data);

			$this->setIndexContratos($contrato->getNumero(), $entry->id);
		}

		$this->log("Iniciando importação de Termos de Contratos!");

		foreach ($this->termosContratos as $i => $termoContrato) {
			$this->log(sprintf("\tAdicionando termo '%s - %s'...", $termoContrato->getContrato(), $termoContrato->getNumeroTermo()));

			$idContrato = $this->getIndexContratos($termoContrato->getContrato());

			$data = array(
				array('name' => 'tlbrx_contrato_tlbrx_termocontratotlbrx_contrato_ida', 'value' => $idContrato),
				array('name' => 'contract_mode',					'value' => $termoContrato->getModalidade()),
				array('name' => 'name', 						'value' => $termoContrato->getNumeroTermo()),
				array('name' => 'currency_id', 						'value' => '-99'),
				array('name' => 'amount', 						'value' => $termoContrato->getValor()),
				array('name' => 'deadline', 						'value' => preg_replace('/\D/', '', $termoContrato->getPrazo())),
				array('name' => 'date_activation', 					'value' => $termoContrato->getDataAtivacao()),
				array('name' => 'account_id1_c',					'value' => $termoContrato->getEnderecoCobranca()),

				array('name' => 'product', 						'value' => $termoContrato->getProduto()),
				array('name' => 'speed', 						'value' => $termoContrato->getVelocidade()),
				array('name' => 'designation',                                          'value' => $termoContrato->getDesignacao()),

				array('name' => 'account_id_c',                                         'value' => $termoContrato->getPontaA()),
				array('name' => 'account_id2_c',                                        'value' => $termoContrato->getPontaB())
			);

			$entry = $this->rest->setEntry('TLBRX_TermoContrato', $data);

			$this->setIndexTermosContratos($termoContrato->getId(), $entry->id);
		}

		$this->log("Iniciando importação de Equipamentos!");

		foreach ($this->equipamentos as $i => $equipamento) {
			$this->log(sprintf("\tAdicionando equipamento '%s - %s[%s]'", $equipamento->getNumeroTermo(), $equipamento->getModeloEquipamento(), $equipamento->getSerial()));

			$idTermoContrato = $this->getIndexTermosContratos($equipamento->getIdTermoContrato());

			$data = array(
				array('name' => 'tlbrx_termocontrato_tlbrx_equipamentotlbrx_termocontrato_ida', 'value' => $idTermoContrato),
				array('name' => 'name', 							'value' => $equipamento->getSerial()),
				array('name' => 'equipment_model', 						'value' => $equipamento->getModeloEquipamento()),
				array('name' => 'patrimony_number', 						'value' => $equipamento->getNumeroPatrimonio())
			);

			$entry = $this->rest->setEntry('TLBRX_Equipamento', $data);		
		}

		$this->log("Importação concluída com sucesso!");
	}

	public function run() {
		$this->initParser();

		$this->showPlanilhas();

		$this->readContratos();
		$this->readTipoContratos();
		$this->readTermoContratos();
		$this->readEquipamentos();

		$this->log(var_export($this->contratos, true), false);
		$this->log(var_export($this->tiposContratos, true), false);
		$this->log(var_export($this->termosContratos, true), false);
		$this->log(var_export($this->equipamentos, true), false);
		
		$this->importToSugarCRM();
	}
}

$parser = new ImportaContratos();
$parser->run();


?>
