<?php

class Equipamento {
	private $idTermoContrato;
	private $numeroTermo;
	private $modeloEquipamento;
	private $serial;
	private $numeroPatrimonio;

	function __construct() {
		$args = func_get_args();

		$this->numeroTermo = isset($args[0]) ? $args[0] : NULL;
		$this->modeloEquipamento = isset($args[1]) ? $args[1] : NULL;
		$this->serial = isset($args[2]) ? $args[2] : NULL;
		$this->patrimonio = isset($args[3]) ? $args[3] : NULL;
	}

	public function setIdTermoContrato($id) {
		$this->idTermoContrato = $id;	
	}

	public function getIdTermoContrato() {
		return $this->idTermoContrato;
	}

	public function setNumeroTermo($numero) {
		$this->numeroTermo = $numero;
	}

	public function getNumeroTermo() {
		return $this->numeroTermo;
	}

	public function setModeloEquipamento($modelo) {
		$this->modeloEquipamento = $modelo;	
	}

	public function getModeloEquipamento() {
		return $this->modeloEquipamento;
	}

	public function setSerial($serial) {
		$this->serial = $serial;
	}

	public function getSerial() {
		return $this->serial;
	}

	public function setNumeroPatrimonio($patrimonio) {
		$this->numeroPatrimonio = $patrimonio;
	}

	public function getNumeroPatrimonio() {
		return $this->numeroPatrimonio;
	}
}
?>
