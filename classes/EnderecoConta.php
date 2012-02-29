<?php
class EquipamentoConta {
	private $endereco;
	private $conta;

	function __construct() {
		$args = func_get_args();
		
		$this->endereco = isset($args[0]) ? $args[0] : NULL;
		$this->conta    = isset($args[1]) ? $args[1] : NULL;
	}

	public function setEndereco($endereco) {
		$this->endereco = $endereco;
	}

	public function getEndereco() {
		return $this->endereco;
	}
	
	public function setConta($conta) {
		$this->conta = $conta;
	}
	
	public function getConta() {
		return $this->conta;
	}
}
?>
