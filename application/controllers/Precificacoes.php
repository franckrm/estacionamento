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
            $this->form_validation->set_rules('precificacao_categoria', 'Categoria', 'trim|required|min_length[5]|max_length[35]|is_unique[precificacoes.precificacao_categoria]');
            $this->form_validation->set_rules('precificacao_valor_hora', 'Valor hora', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('precificacao_valor_mensalidade', 'Valor mensalidade', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('precificacao_numero_vagas', 'Número vagas', 'trim|required|integer|greater_than[0]');
            
            if($this->form_validation->run()){
                $preficicacao_ativa = $this->input->post('precificacao_ativa');

                $data = elements(
                    array(
                        'precificacao_categoria',
                        'precificacao_valor_hora',
                        'precificacao_valor_mensalidade',
                        'precificacao_numero_vagas',
                        'precificacao_ativa',
                    ), $this->input->post()
                );

                $data = html_escape($data);

                $this->core_model->insert('precificacoes', $data);
                redirect($this->router->fetch_class());


            }else{
                $data = array(
                    'titulo' => 'Cadastrar preficicação',
                    'sub_titulo' => 'Chegou a hora de cadastrar precificação',
                    'icone_view'=> 'fas fa-dollar-sign',
                    'scripts' => array(
                        'plugins/mask/jquery.mask.min.js',
                        'plugins/mask/custom.js'
                    )
                );

             
                $this->load->view('layout/header', $data);
                $this->load->view('precificacoes/core');
                $this->load->view('layout/footer');
            }


        }else{
            //Atualizando
            if(!$this->core_model->get_by_id('precificacoes', array('precificacao_id'=>$precificacao_id ))){
                $this->session->set_flashdata('error', 'Precificação não encontrada');
                redirect($this->router->fetch_class());
            }else{

                $this->form_validation->set_rules('precificacao_categoria', 'Categoria', 'trim|required|min_length[5]|max_length[35]|callback_check_categoria');
                $this->form_validation->set_rules('precificacao_valor_hora', 'Valor hora', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('precificacao_valor_mensalidade', 'Valor mensalidade', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('precificacao_numero_vagas', 'Número vagas', 'trim|required|integer|greater_than[0]');
                
                if($this->form_validation->run()){
                    $preficicacao_ativa = $this->input->post('precificacao_ativa');
                    if($preficicacao_ativa==0){
                        if($this->db->table_exists('estacionar') ){
                            if($this->core_model->get_by_id('estacionar', array('estacionar_precificacao_id'=> $precificacao_id, 'estacionar_status'=>0))){
                                $this->session->set_flashdata('error', 'Esta categoria está sendo utilizada em Estacionar');
                            }
                        }
                    }

                    $data = elements(
                        array(
                            'precificacao_categoria',
                            'precificacao_valor_hora',
                            'precificacao_valor_mensalidade',
                            'precificacao_numero_vagas',
                            'precificacao_ativa',
                        ), $this->input->post()
                    );

                    $data = html_escape($data);

                    $this->core_model->update('precificacoes', $data, array('precificacao_id'=>$precificacao_id));
                    redirect($this->router->fetch_class());


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

    public function del($precificacao_id =null){
        if(!$this->core_model->get_by_id('precificacoes', array('precificacao_id'=>$precificacao_id ))){
            $this->session->set_flashdata('error', 'Precificação não encontrada');
            redirect($this->router->fetch_class());
        }

        if($this->core_model->get_by_id('precificacoes', array('precificacao_id'=>$precificacao_id, 'precificacao_ativa'=>1))){
            $this->session->set_flashdata('error', 'Precificação ativa não pode ser excluida');
            redirect($this->router->fetch_class());
        }

        $this->core_model->delete('precificacoes', array('precificacao_id'=>$precificacao_id));
        redirect($this->router->fetch_class());
    }
}
