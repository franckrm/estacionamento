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

            $this->form_validation->set_rules('estacionar_precificacao_id', 'Categoria', 'required');
            $this->form_validation->set_rules('estacionar_numero_vaga', 'Número da vaga', 'required|integer|greater_than[0]|callback_check_vaga_ocupada|callback_check_range_vagas_categoria');
            $this->form_validation->set_rules('estacionar_placa_veiculo', 'Placa veículo', 'required|exact_length[8]|callback_check_placa_status_aberta');
            $this->form_validation->set_rules('estacionar_marca_veiculo', 'Marca veículo', 'required|min_length[2]|max_length[30]');
            $this->form_validation->set_rules('estacionar_modelo_veiculo', 'Modelo veículo', 'required|min_length[2]|max_length[20]');

            if($this->form_validation->run()){

                $data = elements(
                    array(
                        'estacionar_valor_hora',
                        'estacionar_numero_vaga',
                        'estacionar_placa_veiculo',
                        'estacionar_marca_veiculo',
                        'estacionar_modelo_veiculo',

                    ), $this->input->post()
                );


                $data['estacionar_precificacao_id'] = intval(substr($this->input->post('estacionar_precificacao_id'), 0, 1));
                $data['estacionar_status'] = 0; //Ao cadastrar ticket, o valor de 'estacionar_status' fica como '0' 

                $data = html_escape($data);
    
                $this->core_model->insert('estacionar', $data);
                redirect($this->router->fetch_class());

                //Criar método imprimir

            }else{
                //Erro de validação
                $data = array(
                    'titulo' => 'Cadastrar ticket',
                    'sub_titulo' => 'Chegou a hora cadastrar novo ticket de estacionamento',
                    'texto_modal' => 'Tem certeza que deseja salvar esse Ticket? Não será possível alterá-lo',
                    'icone_view' => 'fas fa-parking',
                    'scripts'=>array(
                        'plugins/mask/jquery.mask.min.js',
                        'plugins/mask/custom.js',
                        'js/estacionar/estacionar.js'
                    ),
                    'precificacoes' => $this->core_model->get_all('precificacoes', array('precificacao_ativa' => 1))
                );
                // echo '<pre>';
                // print_r($data['precificacoes']);
                // echo '</pre>';
                // exit;
                $this->load->view('layout/header', $data);
                $this->load->view('estacionar/core');
                $this->load->view('layout/footer');
            }




            
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
                        'sub_titulo' => 'Chegou a hora de encerrar o ticket de estacionamento',
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


    public function check_range_vagas_categoria($numero_vaga) {

        $precificacao_id = intval(substr($this->input->post('estacionar_precificacao_id'), 0, 1));

        if ($precificacao_id) {

            $precificacao = $this->core_model->get_by_id('precificacoes', array('precificacao_id' => $precificacao_id));

            if ($precificacao->precificacao_numero_vagas < $numero_vaga) {

                $this->form_validation->set_message('check_range_vagas_categoria', 'O número da vaga deve estar entre 1 e ' . $precificacao->precificacao_numero_vagas);

                return FALSE;
            } else {

                return TRUE;
            }
        } else {
            $this->form_validation->set_message('check_range_vagas_categoria', 'Escolha uma categoria');
            return FALSE;
        }
    }

    public function check_vaga_ocupada($estacionar_numero_vaga) {

        $estacionar_precificacao_id = intval(substr($this->input->post('estacionar_precificacao_id'), 0, 1));

        if ($this->core_model->get_by_id('estacionar', array('estacionar_numero_vaga' => $estacionar_numero_vaga, 'estacionar_status' => 0, 'estacionar_precificacao_id' => $estacionar_precificacao_id))) {

            $this->form_validation->set_message('check_vaga_ocupada', 'Essa vaga já está ocupada para essa categoria');

            return FALSE;
        } else {

            return TRUE;
        }
    }

    public function check_placa_status_aberta($estacionar_placa_veiculo) {

        $estacionar_placa_veiculo = strtoupper($estacionar_placa_veiculo);

        if ($this->core_model->get_by_id('estacionar', array('estacionar_placa_veiculo' => $estacionar_placa_veiculo, 'estacionar_status' => 0))) {

            $this->form_validation->set_message('check_placa_status_aberta', 'Existe uma ordem aberta para essa placa');

            return FALSE;
        } else {

            return TRUE;
        }
    }

}