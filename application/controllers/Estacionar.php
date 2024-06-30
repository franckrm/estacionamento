<?php

defined('BASEPATH') or exit("Ação não permitida");

class Estacionar extends CI_Controller{

    public function __construct()
	{
		parent::__construct();
		if(!$this->ion_auth->logged_in()){
			redirect('login');
		}

        $this->load->model('estacionar_model');
	}

    public function index()
	{

		$data = array(
			'titulo' => 'Tickets de estacionamento cadastrados',
			'sub_titulo' => 'Chegou a hora de listar os tickets de estacionamentos',
            'icone_view' => 'fas fa-parking',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamento.js',
			),
			'estacionados' => $this->estacionar_model->get_all()
		);
		// echo '<pre>';
		// print_r($data['estacionados']);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('estacionar/index');
		$this->load->view('layout/footer');
	}
    
    public function core($estacionar_id = null)
	{

        if(!$estacionar_id){
            //Cadastrando
        }else{
            if(!$this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id))){
                $this->session->set_flashdata('error','Ticket não encontrado para encerramento');
                redirect($this->router->fetch_class());
            }else{
                //Encerramento do ticket

                $estacionar_tempo_decorrido = str_replace('.', '', $this->input->post('estacionar_tempo_decorrido'));

                //Tornado a forma de pagamento obrigatório se o tempo decorrido for maior de 15 minutos
                if($estacionar_tempo_decorrido > '015'){
                    $this->form_validation->set_rules('estacionar_forma_pagamento_id', "Forma de pagamento", "required");
                }

                if($this->form_validation->run()){

                    $data = elements(
                        array(
                            'estacionar_valor_devido',
                            'estacionar_forma_pagamento_id',
                            'estacionar_tempo_decorrido',

                        ), $this->input->post()
                    );

                    if($estacionar_tempo_decorrido <=015){
                        $data['estacionar_forma_pagamento_id'] = 5; //Forma de pagamento grátis
                    }

                    $data['estacionar_data_saida'] = date('Y-m-d H:m:s');
                    $data['estacionar_status'] = 1; //Encerrando ticket de estacionamento

                    $data = html_escape($data);

                    $this->core_model->update('estacionar', $data, array('estacionar_id' => $estacionar_id));
                    redirect($this->router->fetch_class());

                    //Criar método imprimir

                }else{
                    //Erro de validação
                    $data = array(
                        'titulo' => 'Encerrando ticket',
                        'sub_titulo' => 'Chegou a hora de encerrar o ticket de estacionamentos',
                        'texto_modal' => 'Tem certeza que deseja encerrar esse Ticket?',
                        'icone_view' => 'fas fa-parking',
                        'scripts'=>array(
                            'plugins/mask/jquery.mask.min.js',
                            'plugins/mask/custom.js',
                            'js/estacionar/estacionar.js'
                        ),
                        'estacionado' => $this->core_model->get_by_id('estacionar', array('estacionar_id' => $estacionar_id)),
                        'precificacoes' => $this->core_model->get_all('precificacoes', array('precificacao_ativa' => 1)),
                        'formas_pagamentos' => $this->core_model->get_all('formas_pagamentos', array('forma_pagamento_ativa' => 1))
                    );
                    // echo '<pre>';
                    // print_r($data['precificacoes']);
                    // echo '</pre>';
                    // exit;
                    $this->load->view('layout/header', $data);
                    $this->load->view('estacionar/core');
                    $this->load->view('layout/footer');
                }

               
               

            }
        }

		
	}

}