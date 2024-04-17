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

		$this->form_validation->set_rules('sistema_razao_social', 'Razão social', 'trim|required|min_length[5]|max_length[145]');
		$this->form_validation->set_rules('sistema_nome_fantasia', 'Nome fantasia', 'trim|required|min_length[5]|max_length[145]');
		$this->form_validation->set_rules('sistema_cnpj', 'CPNJ', 'trim|required|exact_length[18]');
		$this->form_validation->set_rules('sistema_ie', 'Inscrição estadual', 'trim|required|min_length[5]|max_length[25]');
		$this->form_validation->set_rules('sistema_telefone_fixo', 'Telefone fixo', 'trim|required|exact_length[14]');
		$this->form_validation->set_rules('sistema_telefone_movel', 'Telefone móvel', 'trim|required|min_length[14]|max_length[15]');
		$this->form_validation->set_rules('sistema_cep', 'CEP', 'trim|required|exact_length[9]');
		$this->form_validation->set_rules('sistema_endereco', 'Endereço', 'trim|required|min_length[5]|max_length[145]');
		$this->form_validation->set_rules('sistema_numero', 'Número', 'trim|required|min_length[1]|max_length[30]');
		$this->form_validation->set_rules('sistema_cidade', 'Cidade', 'trim|required|min_length[4]|max_length[50]');
		$this->form_validation->set_rules('sistema_estado', 'UF', 'trim|required|exact_length[2]');
		$this->form_validation->set_rules('sistema_site_url', 'URL do site', 'trim|required|max_length[100]|valid_url');
		$this->form_validation->set_rules('sistema_email', 'Email de contato', 'trim|required|valid_email|max_length[100]');
		$this->form_validation->set_rules('sistema_texto_ticket', 'Ticket do estacionamento', 'trim|max_length[500]');




	

		if($this->form_validation->run()){

			$data = elements(
				array(
					'sistema_razao_social',
					'sistema_nome_fantasia',
					'sistema_cnpj',
					'sistema_ie',
					'sistema_telefone_fixo',
					'sistema_telefone_movel',
					'sistema_cep',
					'sistema_endereco',
					'sistema_numero',
					'sistema_cidade',
					'sistema_estado',
					'sistema_site_url',
					'sistema_email',
					'sistema_texto_ticket'
				), 
				$this->input->post()
			);
			
			$data = html_escape($data);
			$this->core_model->update('sistema', $data, array('sistema_id'=>1));
			redirect($this->router->fetch_class());

		}else{
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
}
