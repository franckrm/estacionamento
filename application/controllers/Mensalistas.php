<?php

defined('BASEPATH') or exit('Ação  não permitida');

class Mensalistas extends CI_Controller
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
			'titulo' => 'Mensalistas cadastrados',
			'sub_titulo' => 'Chegou a hora de listar os mensalistas cadastrados no banco de dados',
            'icone_view' => 'fas fa-users',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamento.js',
			),
			'mensalistas' => $this->core_model->get_all('mensalistas')
		);
		// echo '<pre>';
		// print_r($data['mensalistas']);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('mensalistas/index');
		$this->load->view('layout/footer');
	}

    public function core($mensalista_id = null)
	{

        if(!$mensalista_id){
            //Cadastrando

        }else{
            if(!$this->core_model->get_by_id('mensalistas', array('mensalista_id'=> $mensalista_id))){
                $this->session->set_flashdata('error', 'Mensalista não encontrada');
                redirect($this->router->fetch_class());
            }else{
                
                $this->form_validation->set_rules('mensalista_nome', 'Nome', 'trim|requied|min_length[4]|max_length[20]');
                
                if($this->form_validation->run()){
                    echo "<pre>";
                    print_r($this->input()->post());
                    exit();
                }else{
                    //Erro de validação
                    $data = array(
                        'titulo' => 'Editar mensalista',
                        'sub_titulo' => 'Chegou a hora de editar o mensalista',
                        'icone_view' => 'fas fa-users',
                        'scripts'=>array(
                            'plugins/mask/jquery.mask.min.js',
                            'plugins/mask/custom.js'
                        ),
                        'mensalista' => $this->core_model->get_by_id('mensalistas', array('mensalista_id'=> $mensalista_id))
                    );
                    // echo '<pre>';
                    // print_r($data['mensalista']);
                    // echo '</pre>';
                    // exit;
                    $this->load->view('layout/header', $data);
                    $this->load->view('mensalistas/core');
                    $this->load->view('layout/footer');
                }
            }
        }
		
	}
}