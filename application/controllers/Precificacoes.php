<?php

defined('BASEPATH') or exit('Ação  não permitida');

class Precificacoes extends CI_Controller
{

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
			'titulo' => 'Precificações cadastradas',
			'sub_titulo' => 'Chegou a hora de listar as  precificações cadastrados no banco de dados',
			'icone_view'=> 'fas fa-dollar-sign',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamento.js',
			),
			'precificacoes' => $this->core_model->get_all('precificacoes'),

		);
		// echo '<pre>';
		// print_r($data['preficicacoes']);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('precificacoes/index');
		$this->load->view('layout/footer');
	}

    public function core($precificacao_id = NULL)
	{
        if(!$precificacao_id){

        }else{
            if(!$this->core_model->get_by_id('precificacoes', array('precificacao_id'=>$precificacao_id ))){
                $this->session->set_flashdata('error', 'Precificação não encontrada');
                redirect($this->router->fetch_class());
            }else{

                $this->form_validation->set_rules('precificacao_categoria', 'Categoria', 'trim|required|min_length[5]|max_length[35]|callback_check_categoria');
                $this->form_validation->set_rules('precificacao_valor_hora', 'Valor hora', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('precificacao_valor_mensalidade', 'Valor mensalidade', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('precificacao_numero_vagas', 'Número vagas', 'trim|required|integer|greater_than[0]');
                if($this->form_validation->run()){
                    echo "<pre>";
                    print_r($this->input->post());
                    exit();
                    
                }else{
                    $data = array(
                        'titulo' => 'Editar preficicação',
                        'sub_titulo' => 'Chegou a hora de editar a  precificação',
                        'icone_view'=> 'fas fa-dollar-sign',
                        'scripts' => array(
                            'plugins/mask/jquery.mask.min.js',
                            'plugins/mask/custom.js'
                        ),
                        'precificacao' => $this->core_model->get_by_id('precificacoes', array('precificacao_id'=>$precificacao_id )),
            
                    );
    
                    // echo "<pre>";
                    // print_r($data['precificacao']);
                    // exit();
                 
                    $this->load->view('layout/header', $data);
                    $this->load->view('precificacoes/core');
                    $this->load->view('layout/footer');
                }
               
            }
        }


		
	}

    public function check_categoria($precificacao_categoria)
    {
        $precificacao_id = $this->input->post('precificacao_id');
        if($this->core_model->get_by_id('precificacoes', array('precificacao_categoria'=>$precificacao_categoria, 'precificacao_id !='=>$precificacao_id))){
            $this->form_validation->set_message('check_categoria', 'Está categoria já existe');
            return false;
        }else{
            return true;
        }
    }
}
