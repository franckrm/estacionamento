<?php 

defined('BASEPATH') or exit('Ação  não permitida');

class Usuarios extends CI_Controller{

	public function index(){

		$data = array(
			'titulo' => 'Usuarios cadastrados',
			'sub_titulo' => 'Chegou a hora de listar os usuários cadastrados no banco de dados',
			'styles'=> array(
				'plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css'
			),
			'scripts' =>array(
				'plugins/datatables.net/js/jquery.dataTables.min.js',
				'plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
				'plugins/datatables.net/js/estacionamento.js',
			),
			'usuarios'=> $this->ion_auth->users()->result(),
			
		);
		// echo '<pre>';
		// print_r($this);
		// echo '</pre>';
		// exit;
		$this->load->view('layout/header', $data);
		$this->load->view('usuarios/index');
		$this->load->view('layout/footer');
	}


	public function core($usuario_id = NULL){

		if(!$usuario_id){
			exit('Pode cadastrar novo usuário');
		}else{
			if(!$this->ion_auth->user($usuario_id)->row()){
				exit('Usuário não existe');
			}else{
				//Editar usuário
				$data = array(
					'titulo' => 'Editar Usuário',
					'sub_titulo' => 'Chegou a hora de editar o usuário',
					'usuario'=> $this->ion_auth->user($usuario_id)->row(),
					'icone_view'=> 'ik ik-user'
					
				);
				$this->load->view('layout/header', $data);
				$this->load->view('usuarios/core');
				$this->load->view('layout/footer');
			}

		}

		
	}

}
