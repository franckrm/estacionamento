<?php

defined('BASEPATH') or exit('Ação  não permitida');

class Usuarios extends CI_Controller
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


        if(!$this->ion_auth->is_admin()){
            $this->session->set_flashdata('info', 'Você não tem permissão para acessar esse Menu');
            redirect('/');
        }

		$data = array(
			'titulo' => 'Usuarios cadastrados',
			'sub_titulo' => 'Chegou a hora de listar os usuários cadastrados no banco de dados',
			'styles' => array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' => array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamento.js',
			),
			'usuarios' => $this->ion_auth->users()->result(),

		);
		// echo '<pre>';
		// print_r($this);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('usuarios/index');
		$this->load->view('layout/footer');
	}


	public function core($usuario_id = NULL)
	{

		if (!$usuario_id) {
			//Cadastro de novo usuário

            if(!$this->ion_auth->is_admin()){
                $this->session->set_flashdata('info', 'Você não tem permissão para acessar esse Menu');
                redirect('/');
            }
			
			$this->form_validation->set_rules('first_name', 'Nome', 'trim|required|min_length[3]|max_length[20]');
			$this->form_validation->set_rules('last_name', 'Sobrenome', 'trim|required|min_length[3]|max_length[20]');
			$this->form_validation->set_rules('username', 'Usuário', 'trim|required|min_length[3]|max_length[30]|is_unique[users.username]');
			$this->form_validation->set_rules('email', 'E-mail', 'trim|valid_email|required|min_length[5]|max_length[200]|is_unique[users.email]');
			$this->form_validation->set_rules('password', 'Senha', 'trim|required|min_length[8]');
			$this->form_validation->set_rules('confirmacao', 'Confirmação', 'trim|required|matches[password]');

			if($this->form_validation->run()){
				
				$username = html_escape($this->input->post('username'));
				$password = html_escape($this->input->post('password'));
				$email = html_escape($this->input->post('email'));
				$additional_data = array(
							'first_name' => $this->input->post('first_name'),
							'last_name' => $this->input->post('last_name'),
							'active' => $this->input->post('active'),
							);
				$group = array($this->input->post('perfil'));
			
				$additional_data = html_escape($additional_data);

				if($this->ion_auth->register($username, $password, $email, $additional_data, $group)){
					$this->session->set_flashdata('sucesso', 'Dados salvos com sucesso!');
				}else{
					$this->session->set_flashdata('error', 'Erro ao salvar os dados!');
				}

				redirect($this->router->fetch_class());
				
			}else{
				$data = array(
					'titulo' => 'Cadastrar Usuário',
					'sub_titulo' => 'Chegou a hora de cadastrar um novo usuário',
					'icone_view' => 'ik ik-user',

				);

				// echo '<pre>';
				// print_r($data['perfil_usuario']);
				// exit();

				$this->load->view('layout/header', $data);
				$this->load->view('usuarios/core');
				$this->load->view('layout/footer');
			}

		} else {
			if (!$this->ion_auth->user($usuario_id)->row()) {
				exit('Usuário não existe');
			} else {
				//Editar usuário

                if($this->session->userdata('user_id')!= $usuario_id && !$this->ion_auth->is_admin()){
                    $this->session->set_flashdata('error', 'Você não pode editar um usuário diferente do seu');
                    redirect('/');
                }

				$perfil_atual = $this->ion_auth->get_users_groups($usuario_id)->row();
				
				$this->form_validation->set_rules('first_name', 'Nome', 'trim|required|min_length[3]|max_length[20]');
				$this->form_validation->set_rules('last_name', 'Sobrenome', 'trim|required|min_length[3]|max_length[20]');
				$this->form_validation->set_rules('username', 'Usuário', 'trim|required|min_length[3]|max_length[30]|callback_username_check');
				$this->form_validation->set_rules('email', 'E-mail', 'trim|valid_email|required|min_length[5]|max_length[200]|callback_email_check');
				$this->form_validation->set_rules('password', 'Senha', 'trim|min_length[8]');
				$this->form_validation->set_rules('confirmacao', 'Confirmação', 'trim|matches[password]');

				if ($this->form_validation->run()) {
					
					$data = elements(
						array(
							'first_name',
							'last_name',
							'username',
							'email',
							'password',
							'active'
						),
						$this->input->post() 
					);

                    if(!$this->ion_auth->is_admin()){
                        unset($data['active']);
                    }
					$password = $this->input->post('password');

					//Não atualiza a senha
					if(!$password){
						unset($data['password']);
					}

					$data = html_escape($data);

					if($this->ion_auth->update($usuario_id, $data)){

						$perfil_post = $this->input->post('perfil');
						
                        //Se foi passado o 'perfil' então é admin
						if($perfil_post){
                            //Se for diferente atualiza o grupo
                            if($perfil_atual->id != $perfil_post){
                                $this->ion_auth->remove_from_group($perfil_atual->id, $usuario_id);
                                $this->ion_auth->add_to_group($perfil_post, $usuario_id);
                            }
                        }

						$this->session->set_flashdata('sucesso', 'Dados atualizados com sucesso');
					}else{
						$this->session->set_flashdata('error', 'Não foi possível atualizar os dados');
					}

                    if(!$this->ion_auth->is_admin()){
                        redirect('/');
                    }

					redirect($this->router->fetch_class());
				} else {
					//Erro da validação

					$data = array(
						'titulo' => 'Editar Usuário',
						'sub_titulo' => 'Chegou a hora de editar o usuário',
						'icone_view' => 'ik ik-user',
						'usuario' => $this->ion_auth->user($usuario_id)->row(),
						'perfil_usuario' =>  $this->ion_auth->get_users_groups($usuario_id)->row()


					);

					// echo '<pre>';
					// print_r($data['perfil_usuario']);
					// exit();

					$this->load->view('layout/header', $data);
					$this->load->view('usuarios/core');
					$this->load->view('layout/footer');
				}
			}
		}
	}

    public function username_check($username)
    {
            $usuario_id = $this->input->post('usuario_id');
	
            if($this->core_model->get_by_id('users', array('username'=>$username, 'id !=' => $usuario_id))){
                $this->form_validation->set_message('username_check', 'Esse usuário já existe.');
                return false;
            }else{
                return true;
            }
    }

	public function email_check($email)
    {
            $usuario_id = $this->input->post('usuario_id');
	
            if($this->core_model->get_by_id('users', array('email'=>$email, 'id !=' => $usuario_id))){
                $this->form_validation->set_message('email_check', 'Esse e-mail já existe.');
                return false;
            }else{
                return true;
            }
    }

	public function del($usuario_id =null){

        if(!$this->ion_auth->is_admin()){
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar esse Menu');
            redirect('/');
        }

		if(!$usuario_id || !$this->core_model->get_by_id('users', array('id'=>$usuario_id))){
			$this->session->set_flashdata('error', 'Usuário não encontrado.');
			var_dump($this->session->flashdata('error'));
			redirect($this->router->fetch_class());
		}else{
			if($this->ion_auth->is_admin($usuario_id)){
				$this->session->set_flashdata('error', 'Administrador não pode ser excluido.');
				redirect($this->router->fetch_class());
			}

			if($this->ion_auth->delete_user($usuario_id)){
				$this->session->set_flashdata('sucesso', 'Registro exclído com sucesso!');	
			}else{
				$this->session->set_flashdata('error', 'Não foi possível excluir o registro.');
			}

			redirect($this->router->fetch_class());
		}
	}
}
