<?php

defined('BASEPATH') or exit("Ação não permitida");

class Mensalidades extends CI_Controller{

    public function __construct()
	{
		parent::__construct();
		if(!$this->ion_auth->logged_in()){
			redirect('login');
		}

        $this->load->model('mensalidades_model');
	}

    public function index()
	{

		$data = array(
			'titulo' => 'Mensalidades cadastrados',
			'sub_titulo' => 'Chegou a hora de listar as mensalidades cadastradas no banco de dados',
            'icone_view' => 'fas fa-hand-holding-usd',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamento.js',
			),
			'mensalidades' => $this->mensalidades_model->get_all()
		);
		// echo '<pre>';
		// print_r($data['mensalidades']);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('mensalidades/index');
		$this->load->view('layout/footer');
	}


}