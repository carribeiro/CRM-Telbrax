<?php
require CLASS_PATH.'/spider_generic/spider.php';

class RestSugarCRM extends Spider {
	private $restUrl;
	private $session;

	function __construct($url) {
		parent::__construct();
		
		$this->restUrl = sprintf('%s/service/v4/rest.php', $url);;
	}

	public function doLogin($user, $pass) {
		$user_auth = array(
			'user_auth' => array(
				'user_name'       => $user,
				'password'        => md5($pass),
				'version'         => '.01',
				'name_value_list' => array(
					array(
						'name'  => 'notifyonsave',
						'value' => 'false'
					)
				)
			),
			'application_name' => 'TelbraxImportData'
		);

		$this->session = $this->doRequest('login', $user_auth);
	}

	public function doRequest($method, $data) {
		$data = array(
			'method' 	=> $method,
			'input_type' 	=> 'json',
			'response_type'	=> 'json',
			'rest_data'	=> json_encode($data)
		);

		$return = $this->doPost($this->restUrl, $data);

		return json_decode($return);		
	}

	public function setEntry($module, $name_value_list) {
		$insert = array (
			'session' => $this->session->id,
			'module'  => $module,
			'name_value_list' => $name_value_list	
		);
	
		return $this->doRequest('set_entry', $insert);
	}


	public function getEntry($module, $id) {
		$insert = array (
			'session' => $this->session->id,
			'module'  => $module,
			'id'	  => $id
		);
	
		return $this->doRequest('set_entry', $insert);
	}

	public function getEntryList($module, $query) {
		$search = array(
			'session' 	=> $this->session->id,
			'module_name'	=> $module,
			'query' 	=> $query
		);

		return $this->doRequest('get_entry_list', $search);
	}

	public function setRelationship() {
		
	}
}
/* USAGE
$rest = new SugarCRMRest('http://192.168.134.150/sugarcrm');
$rest->doLogin('vinicius', 'pass');


$rest->setEntry('TLBRX_TipoContrato', array(
                                array('name' => 'document_name', 'value' => 'Contrato criado por REST '.rand()),
                                array('name' => 'product_list', 'value' => '^connect_flex^,^connect_flex_multi^')
              ));
*/
?>
