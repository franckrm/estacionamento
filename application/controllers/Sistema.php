<?php
defined('BASEPATH') or exit('Ação não permitida');


class Sistema extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		if(!$this->ion_auth->logged_in()){
			redirect('login');
		}
	}

	public function index(){
		$data = array(
			'titulo' => 'Editar Informações do sistema',
			'sub_titulo' => 'Chegou a hora de editar as informações do  sisterma',
			'icone_view' => 'ik ik-settings',
			'sistema' => $this->core_model->get_by_id('sistema', array('sistema_id'=> 1))

		);

		var_dump($data['sistema']); 

		$this->load->view('layout/header', $data);
		$this->load->view('usuarios/core');
		$this->load->view('layout/footer');
	}
}
