<?php
require_once 'soap/SoapRelationshipHelper.php';
require_once 'DateClass.php';

class CreateLancamento {
	function createLancamentoUnico($contrato, $termos) {
		$periodos = array();

		foreach ($termos as $termo) {
			foreach ($termo->vencimentosCalculados as $periodo => $vencimentos) {
				foreach ($vencimentos as $vencimento) {
					$periodos[$periodo][] = $vencimento;
				}
			}		
		}

		foreach ($periodos as $periodo => $lancamentos) {
			$dados = array(
				'valor' => 0.0,
				'inicio' => '',
				'final' => '',
				'descricao' => ''
			);
			foreach ($lancamentos as $lancamento) {
				$dados['valor']	    += $lancamento['valor'];
				$dados['inicio']     = $lancamento['inicio'];
				$dados['final']      = $lancamento['final'];
				$dados['descricao'] .= sprintf("[%s] %s -> R$%0.02f\n", $lancamento['termo'], $lancamento['produto'], $lancamento['valor']);
			}
			var_dump($dados);
		}


		#var_dump($periodos);
	}

	function createLancamentoPorCNPJ($contrato, $termos) {
		
	}

	function dateToClass($date, $delimiter = '-') {
		$date = explode($delimiter, $date);

		return new DateClass(mktime(0, 0, 0, $date[1], $date[0], $date[2]));
	}

	function calculaVencimentos($termo) {
		global $app_list_strings;
		
		$dataInicial = $termo->date_activation;
		$meses = $termo->deadline;
		$valor = $termo->amount;
		$modalidade = $termo->contract_mode;
		$produto = $app_list_strings['telbrax_product_list'][$termo->product];
		$nome = $termo->name;


		$dataInicial = self::dateToClass($dataInicial);
		$dataFinal = new DateClass($dataInicial->TimeStamp());
		$dataFinal = $dataFinal->Add('month', +$meses);
		
		$inicioVencimento = $dataInicial->Add('day', 0);
		$finalVencimento = $dataInicial->Add('day', 0);
		$periodos = array();

		while ($inicioVencimento->TimeStamp() <= $dataFinal->TimeStamp()) {
			$finalVencimento = $inicioVencimento->EOM();

			if ($finalVencimento->TimeStamp() > $dataFinal->TimeStamp()) {
				$finalVencimento = $dataFinal->Add('day', 0); 
			}

			//calcula percentual de valor para intervalos quebrados de dias. Ex.: 12/03/2010 a 31/03/2010 
			$valorCalculado = $valor * (($finalVencimento->Day() - $inicioVencimento->Day() + 1) / $inicioVencimento->DIM());

			$intervalo = array(
				'inicio' => $inicioVencimento->ToString('d-m-Y'),
				'final'  => $finalVencimento->ToString('d-m-Y'),
				'valor'  => $valorCalculado,
				'produto' => $produto,
				'termo' => $nome
			);


			array_push($periodos, $intervalo);

			#echo "inicio: ", $inicioVencimento->ToString(), " final: ", $finalVencimento->ToString(), " valor calculado: ", $valorCalculado, "\n";
			
			$inicioVencimento = $inicioVencimento->Add('month', 1);
			$inicioVencimento = $inicioVencimento->BOM();
		}

		$lancamentos = array();

		$dataFaturamento = $dataInicial->Add('day', 0);

		if ($modalidade == 'mes_fechado_pre') {
			$dataFaturamento = $dataFaturamento->Add('month', 1);
			
			$data = sprintf('%s-%s', $dataFaturamento->Year(), $dataFaturamento->Month());
			
			$lancamentos[$data] = array_splice($periodos, 0, 2);
		}
		
		while (count($periodos)) {		
			$dataFaturamento = $dataFaturamento->Add('month', 1);

			$data = sprintf('%s-%s', $dataFaturamento->Year(), $dataFaturamento->Month());

			$item =  array_shift($periodos);

			$lancamentos[$data][] = $item;
		}

		return $lancamentos;
	}

	function lancamentoManager(&$bean, $event, $arguments) {
		#var_dump($bean->tlbrx_contrato_tlbrx_termocontrato->focus->custom_fields->bean->tlbrx_contrato_tlbrx_termocontratotlbrx_contrato_ida);
		#var_dump($bean->tlbrx_contrato_tlbrx_termocontratotlbrx_contrato_ida);
		#var_dump($event);
		#var_dump($arguments);
		echo '<pre>';

		$id_contrato = current($bean->tlbrx_contrato_tlbrx_termocontrato->get());

		$contrato = new TLBRX_Contrato();
		$contrato->retrieve($id_contrato);
		$contrato->load_relationships();

		$ids_termos = $contrato->tlbrx_contrato_tlbrx_termocontrato->get();

		$termos = array();

		foreach ($ids_termos as $index => $id_termo) {
			$termo = new TLBRX_TermoContrato();
			$termo->retrieve($id_termo);

			#echo "##################", $termo->date_activation, "#########################\n";
			$termo->vencimentosCalculados = self::calculaVencimentos($termo);

			array_push($termos, $termo);
		}

		if ($contrato->invoice_type == 'por_cnpj') {
			self::createLancamentoPorCNPJ($contrato, $termos);
		} else {
			self::createLancamentoUnico($contrato, $termos);
		}

		#var_dump($termos);
		#var_dump($contrato->tlbrx_contrato_tlbrx_termocontrato);

		#var_dump($contrato->tlbrx_contrato_tlbrx_termocontrato->get());

		#var_dump(retrieve_relationships_properties('TLBRX_TermoContrato', 'TLBRX_Contrato'));
		#var_dump(get_class_methods('TLBRX_Contrato'));
		
		#var_dump(new TLBRX_Lancamento());

		#var_dump(new DateClass());
		exit;
	}
}
?>
