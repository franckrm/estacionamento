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
                
                $this->form_validation->set_rules('mensalista_nome', 'Nome', 'trim|required|min_length[4]|max_length[20]');
                $this->form_validation->set_rules('mensalista_sobrenome', 'Sobrenome', 'trim|required|min_length[4]|max_length[150]');
                $this->form_validation->set_rules('mensalista_data_nascimento', 'Data nascimento', 'required');
                $this->form_validation->set_rules('mensalista_cpf', 'CPF', 'trim|required|exact_length[14]|callback_valida_cpf');
                $this->form_validation->set_rules('mensalista_rg', 'RG', 'trim|required|min_length[12]|max_length[14]|callback_check_rg');
                $this->form_validation->set_rules('mensalista_email', 'E-mail', 'trim|required|valid_email|max_length[50]|callback_check_email');
                
                $mensalista_telefone_fixo = $this->input->post('mensalista_telefone_fixo');

                if(!empty($mensalista_telefone_fixo)){
                    $this->form_validation->set_rules('mensalista_telefone_fixo', 'Telefone fixo', 'trim|exact_length[14]|callback_check_telefone_fixo');
                }

               
                $this->form_validation->set_rules('mensalista_telefone_movel', 'Telefone móvel', 'trim|min_length[14]|max_length[15]|callback_check_telefone_movel');
               
                if($this->form_validation->run()){
                    echo "validou";
                    echo "<pre>";
                    print_r($this->input->post());
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

    public function valida_cpf($cpf) {

        if ($this->input->post('mensalista_id')) {

            $mensalista_id = $this->input->post('mensalista_id');

            if ($this->core_model->get_by_id('mensalistas', array('mensalista_id !=' => $mensalista_id, 'mensalista_cpf' => $cpf))) {
                $this->form_validation->set_message('valida_cpf', 'O campo {field} já existe, ele deve ser único');
                return FALSE;
            }
        }

        $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);
        // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {

            $this->form_validation->set_message('valida_cpf', 'Por favor digite um CPF válido');
            return FALSE;
        } else {
            // Calcula os números para verificar se o CPF é verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c); 
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    $this->form_validation->set_message('valida_cpf', 'Por favor digite um CPF válido');
                    return FALSE;
                }
            }
            return TRUE;
        }
    }

    public function check_rg($mensalista_rg){
        $mensalista_id = $this->input->post('mensalista_id');

        if ($this->core_model->get_by_id('mensalistas', array('mensalista_id !=' => $mensalista_id, 'mensalista_rg' => $mensalista_rg))) {
            $this->form_validation->set_message('check_rg', 'O campo {field} já existe, ele deve ser único');
            return FALSE;
        }else{
            return true;
        }
    }


    public function check_email($mensalista_email){
        $mensalista_id = $this->input->post('mensalista_id');

        if ($this->core_model->get_by_id('mensalistas', array('mensalista_id !=' => $mensalista_id, 'mensalista_email' => $mensalista_email))) {
            $this->form_validation->set_message('check_email', 'O campo {field} já existe, ele deve ser único');
            return FALSE;
        }else{
            return true;
        }
    }

    public function check_telefone_fixo($mensalista_telefone_fixo){
        $mensalista_id = $this->input->post('mensalista_id');

        if ($this->core_model->get_by_id('mensalistas', array('mensalista_id !=' => $mensalista_id, 'mensalista_telefone_fixo' => $mensalista_telefone_fixo))) {
            $this->form_validation->set_message('check_telefone_fixo', 'O campo {field} já existe, ele deve ser único');
            return FALSE;
        }else{
            return true;
        }
    }

    public function check_telefone_movel($mensalista_telefone_movel){
        $mensalista_id = $this->input->post('mensalista_id');

        if ($this->core_model->get_by_id('mensalistas', array('mensalista_id !=' => $mensalista_id, 'mensalista_telefone_movel' => $mensalista_telefone_movel))) {
            $this->form_validation->set_message('check_telefone_movel', 'O campo {field} já existe, ele deve ser único');
            return FALSE;
        }else{
            return true;
        }
    }
}