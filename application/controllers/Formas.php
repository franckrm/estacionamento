<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Formas extends CI_Controller {
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
			'titulo' => 'Formas de pagamento cadastradas',
			'sub_titulo' => 'Chegou a hora de listar as  formas de pagamento cadastradas no banco de dados',
			'icone_view'=> 'fas fa-comment-dollar',
			'formas' => $this->core_model->get_all('formas_pagamentos'),

		);
		// echo '<pre>';
		// print_r($data['preficicacoes']);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('formas/index');
		$this->load->view('layout/footer');
	}

}