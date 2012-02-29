<?php
class TipoContrato {
	private $id;
	private $nome;
	private $produtos;
	private $descricao;

	function __construct() {
		$args = func_get_args();

		$this->id = isset($args[0]) ? $args[0] : 0;
		$this->nome = isset($args[1]) ? $args[1] : '';
		$this->produtos = isset($args[2]) ? $args[2] :  array();
		$this->descricao = isset($args[3]) ? $args[3] : '';
	}

	public function getId() {
		return $this->id;
	}

	public function getNome() {
		return $this->nome;
	}

	public function getProdutos() {
		return $this->produtos;
	}

	public function getDescricao() {
		return $this->descricao;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setNome($nome) {
		$this->nome = $nome;
	}

	public function setProdutos($produtos) {
		$this->produtos = $produtos;
	}

	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}
}
?>
