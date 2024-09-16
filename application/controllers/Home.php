<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!$this->ion_auth->logged_in()){
			redirect('login');
		}
	}


	public function index()
	{

		$data = array(
			'titulo' => 'Home',
			'sub_titulo' => 'Seja muito bem vindo(a) ao Park Now!',
            'icone_view' => 'ik ik-home',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
                'dist/css/estacionar.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamento.js',
			),
            /*Inicio numero vagas por categoria*/
            'numero_vagas_pequeno' =>$this->estacionar_model->get_numero_vagas(1), //Veículo pequeno
            'vagas_ocupadas_pequeno' => $this->core_model->get_all('estacionar', array('estacionar_status' =>0, 'estacionar_precificacao_id' =>1)),

            'numero_vagas_medio' =>$this->estacionar_model->get_numero_vagas(2), //Veículo médio
            'vagas_ocupadas_medio' => $this->core_model->get_all('estacionar', array('estacionar_status' =>0, 'estacionar_precificacao_id' =>2)),

            'numero_vagas_grande' =>$this->estacionar_model->get_numero_vagas(3), //Veículo grande
            'vagas_ocupadas_grande' => $this->core_model->get_all('estacionar', array('estacionar_status' =>0, 'estacionar_precificacao_id' =>3)),

            'numero_vagas_moto' =>$this->estacionar_model->get_numero_vagas(4), //Veículo moto
            'vagas_ocupadas_moto' => $this->core_model->get_all('estacionar', array('estacionar_status' =>0, 'estacionar_precificacao_id' =>4)),

		);
		// echo '<pre>';
		// print_r($data['estacionados']);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('home/index');
		$this->load->view('layout/footer');
	}
    
}
