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
			'scripts'=>array(
				'plugins/mask/jquery.mask.min.js',
				'plugins/mask/custom.js'
			),
			'sistema' => $this->core_model->get_by_id('sistema', array('sistema_id'=> 1))

		);

		//var_dump($data['sistema']); die;

		$this->load->view('layout/header', $data);
		$this->load->view('sistema/index');
		$this->load->view('layout/footer');
	}
}
