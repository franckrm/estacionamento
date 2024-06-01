
<?php $this->load->view('layout/navbar');?>

<div class="page-wrap">
<?php $this->load->view('layout/sidebar') ?>
<div class="main-content">
	<div class="container-fluid">
		<div class="page-header">
			<div class="row align-items-end">
				<div class="col-lg-8">
					<div class="page-header-title">
                        <i class="<?php echo $icone_view; ?> bg-blue"></i>
						<div class="d-inline">
							<h5><?php echo $titulo;?></h5>
							<span><?php echo $sub_titulo;?></span>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<nav class="breadcrumb-container" aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item">
								<a title="Home" href="<?php echo base_url(); ?>"><i class="ik ik-home"></i></a>
							</li>
							<li class="breadcrumb-item active" aria-current="page"><?php echo $titulo;?></li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
		<?php if($message = $this->session->flashdata('sucesso')):?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert bg-success alert-success text-white alert-dismissible fade show" role="alert">
						<strong></i> <?php echo $message ?></strong>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<i class="ik ik-x"></i>
						</button>
					</div>	
				</div>
			</div>
		<?php endif; ?>
		<?php if($message = $this->session->flashdata('error')):?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert bg-danger alert-danger text-white alert-dismissible fade show" role="alert">
						<strong></i> <?php echo $message ?></strong>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<i class="ik ik-x"></i>
						</button>
					</div>	
				</div>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header d-block"><a  data-toggle="tooltip" data-placement="right" title="Cadastrar usuário" class="btn bg-blue float-right text-white" href="<?php echo base_url($this->router->fetch_class().'/'.'core')?>">+ Novo</a></div>
					<div class="card-body">
						<table class="table data-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Mensalista</th>
									<th>CPF</th>
									<th>Categoria</th>
                                    <th>Valor mensalidade</th>
                                    <th>Data de vencimento</th>
                                    <th>Data de pagamento</th>
                                    <th>Status</th>
									<th class="nosort text-right pr-25">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($mensalidades as $mensalidade): ?>
									<tr>
										<td><?php echo $mensalidade->mensalidade_id ?></td>
										<td><?php echo $mensalidade->mensalista_nome ?></td>
										<td><?php echo $mensalidade->mensalista_cpf ?></td>
                                        <td><?php echo $mensalidade->precificacao_categoria ?></td>
										<td><?php echo 'R$ '.$mensalidade->precificacao_valor_mensalidade ?></td>
                                        <td><?php echo  formata_data_banco_sem_hora($mensalidade->mensalidade_data_vencimento) ?></td>
                                        <td><?php echo ($mensalidade->mensalidade_status == 1? formata_data_banco_sem_hora($mensalidade->mensalidade_data_pagamento) : 'Em aberto') ?></td>
										<td><?php echo ($mensalidade->mensalidade_status == 1?'<span class="badge badge-pill badge-success mb-1">Paga</span>' : '<span class="badge badge-pill badge-warning mb-1">Em aberto</span>'); ?></td>
										<td class="text-right">
											<a data-toggle="tooltip" data-placement="bottom" title="Editar <?php echo $this->router->fetch_class();?>" href="<?php echo base_url($this->router->fetch_class().'/core/'.$mensalidade->mensalidade_id)?>" class="btn btn-icon btn-primary"><i class="ik ik-edit-2"></i></a>
											<button  type="button" title="Excluir <?php echo $this->router->fetch_class();?>" class="btn btn-icon btn-danger"  data-toggle="modal" data-target="#mensalidade-<?php echo $mensalidade->mensalidade_id ?>"><i class="ik ik-trash-2"></i></button> 
											
										</td>
									</tr>
									<div class="modal fade" id="mensalidade-<?php echo $mensalidade->mensalidade_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalCenterLabel"><i class="fas fa-exclamation-triangle text-danger"></i> Tem certeza da exclusão do registro?</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												</div> 
												<div class="modal-body">
													<p>Se deseja excluir o registro, clique em <strong>Sim, exlucir</strong></p>
												</div>
												<div class="modal-footer">
													<button data-toggle="tooltip" data-placement="bottom" title="Cancelar Exclusão" type="button" class="btn btn-secondary" data-dismiss="modal">Não, voltar</button>
													<a data-toggle="tooltip" data-placement="bottom" title="Excluir <?php echo $this->router->fetch_class();?>" href="<?php echo base_url($this->router->fetch_class().'/del/'.$mensalidade->mensalidade_id)?>" class="btn btn-danger">Sim, excluir</a>
												</div>
											</div>
										</div>
                        			</div>
									
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>


<footer class="footer">
	<div class="w-100 clearfix">
		<span class="text-center text-sm-left d-md-inline-block">Copyright © <?php echo date('Y')?> ThemeKit v2.0. All Rights Reserved.</span>
		<span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Crafted with <i class="fa fa-code text-dark"></i> by <a href="javascript:void" class="text-dark" target="_blank">Francisco Miguel</a></span>
	</div>
</footer>

</div>

        