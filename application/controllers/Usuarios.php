<?php 

defined('BASEPATH') or exit('Ação  não permitida');

class Usuarios extends CI_Controller{

	public function index(){

		$data = array(
			'titulo' => 'Usuarios cadastrados',
			'sub_titulo' => 'Listando todos os usuários cadastrados',
			'titulo_tabela' => 'Listando os usuários',
			'usuarios'=> $this->ion_auth->users()->result(),
			
		);
		// echo '<pre>';
		// print_r($this);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('usuarios/index');
		$this->load->view('layout/footer');
	}

}
