<?php
require CLASS_PATH.'/TipoContrato.php';

class Contrato {
	private $numero;
	private $conta;
	private $tipoContrato;
	private $dataAssinatura;
	private $status;
	private $tipoFatura;
	private $atribuido;

	function __construct() {
		$args = func_get_args();

		$this->numero = isset($args[0]) ? $args[0] : NULL;
		$this->conta = isset($args[1]) ? $args[1] : NULL;
		$this->tipoContrato = isset($args[2]) ? $args[2] : NULL;
		$this->dataAssinatura = isset($args[3]) ? $args[3] : NULL;
		$this->status = isset($args[4]) ? $args[4] : NULL;
		$this->tipoFatura = isset($args[5]) ? $args[5] : NULL;
		$this->atribuido = isset($args[6]) ? $args[6] : NULL;
	}

	public function setNumero($numero) {
		$this->numero = $numero;
	}

	public function getNumero() {
		return $this->numero;
	}

	public function setConta($conta) {
		$this->conta = $conta;	
	}

	public function getConta() {
		return $this->conta;
	}

	public function setTipoContrato($tipoContrato) {
		$this->tipoContrato = $tipoContrato;
	}

	public function getTipoContrato() {
		return $this->tipoContrato;
	}

	public function setDataAssinatura($dataAssinatura) {
		$this->dataAssinatura = $dataAssinatura;
	}

	public function getDataAssinatura() {
		return $this->dataAssinatura;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setTipoFatura($tipoFatura) {
		$this->tipoFatura = $tipoFatura;
	}

	public function getTipoFatura() {
		return $this->tipoFatura;
	}

	public function setAtribuido($atribuido) {
		$this->atribuido = $atribuido;
	}

	public function getAtribuido() {
		return $this->atribuido;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}
}
?>
