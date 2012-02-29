<?php
class TermoContrato {
	private $id;
	private $contrato;
	private $numeroTermo;
	private $modalidade;
	private $valor;
	private $ativacao;
	private $prazo;
	private $dataAtivacao;
	private $enderecoCobranca;

	private $produto;
	private $velocidade;
	private $designacao;

	private $pontaA;
	private $pontaB;

	function __construct() {
		$args = func_get_args();

		$this->contrato = isset($args[0]) ? $args[0] : NULL;
		$this->numeroTermo = isset($args[1]) ? $args[1] : NULL;
		$this->modalidade = isset($args[2]) ? $args[2] : NULL;
		$this->valor = isset($args[3]) ? $args[3] : NULL;
		$this->ativacao = isset($args[4]) ? $args[4] : NULL;
		$this->prazo = isset($args[5]) ? $args[5] : NULL;
		$this->dataAtivacao = isset($args[6]) ? $args[6] : NULL;
		$this->enderecoCobranca = isset($args[7]) ? $args[7] : NULL;
		
		$this->produto = isset($args[8]) ? $args[8] : NULL;
		$this->velocidade = isset($args[9]) ? $args[9] : NULL;
		$this->designacao = isset($args[10]) ? $args[10] : NULL;

		$this->pontaA = isset($args[11]) ? $args[11] : NULL;
		$this->pontaB = isset($args[12]) ? $args[12] : NULL;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setContrato($contrato) {
		$this->contrato = $contrato;
	}

	public function getContrato() {
		return $this->contrato;
	}

	public function setNumeroTermo($numeroTermo) {
		$this->numeroTermo = $numeroTermo;	
	}

	public function getNumeroTermo() {
		return $this->numeroTermo;
	}

	public function setModalidade($modalidade) {
		$this->modalidade = $modalidade;
	}

	public function getModalidade() {
		return $this->modalidade;
	}

	public function setValor($valor) {
		$this->valor = $valor;
	}

	public function getValor() {
		return $this->valor;
	}

	public function setAtivacao($ativacao) {
		$this->ativacao = $ativacao;
	}

	public function getAtivacao() {
		return $this->ativacao;
	}

	public function setPrazo($prazo) {
		$this->prazo = $prazo;
	}

	public function getPrazo() {
		return $this->prazo;
	}

	public function setDataAtivacao($dataAtivacao) {
		$this->dataAtivacao = $dataAtivacao;
	}

	public function getDataAtivacao() {
		return $this->dataAtivacao;
	}

	public function setEnderecoCobranca($enderecoCobranca) {
		$this->enderecoCobranca = $enderecoCobranca;
	}

	public function getEnderecoCobranca() {
		return $this->enderecoCobranca;
	}

	public function setProduto($produto) {
		$this->produto = $produto;
	}

	public function getProduto() {
		return $this->produto;
	}

	public function setVelocidade($velocidade) {
		$this->velocidade = $velocidade;
	}

	public function getVelocidade() {
		return $this->velocidade;		
	}

	public function setDesignacao($designacao) {
		$this->designacao = $designacao;
	}

	public function getDesignacao() {
		return $this->designacao;
	}

	public function setPontaA($ponta) {
		$this->pontaA = $ponta;
	}

	public function getPontaA() {
		return $this->pontaA;
	}

	public function setPontaB($ponta) {
		$this->pontaB = $ponta;
	}

	public function getPontaB() {
		return $this->pontaB;
	}
}
?>
