<?php
include_once ("include.php");
include_once ($_SESSION['pathSys']."BDconfig.php");
include_once ($_SESSION['pathSys']."revisaSesion.php");
include_once ($_SESSION['pathCla']."Conexion.class.php");
include_once ($_SESSION['pathCla']."Revista.class.php");
$db    = new Conexion( $_dbhost, $_dbuname, $_dbpass, $_dbname, $persistency = true );
$revista = new Revista ($db,$_SESSION,$_POST,Comunes::LISTAR,Comunes::LISTAR,Comunes::LISTAR);
$registros = $revista->obtenRegistros();
include_once ($_SESSION['pathSys'] . "headerAdmin.php");
?>

	<!-- Preloader -->
	<div class="preloader-it">
		<div class="la-anim-1"></div>
	</div>
	<!-- /Preloader -->
    <div class="wrapper box-layout theme-1-active pimary-color-pink">
		<!-- Top Menu Items -->
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<?php require_once($_SESSION ['pathSys']."menuSuperior.php"); ?>
		</nav>		
		<!-- /Top Menu Items -->
		<div class="fixed-sidebar-left">
			<?php require_once($_SESSION ['pathSys']."menu.php"); ?>
		</div>
		<!-- /Left Sidebar Menu -->    
        <!-- Main Content -->
		<div class="page-wrapper">
            <div class="container-fluid">
				
				<!-- Title -->
				<div class="row heading-bg">
					<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
					  <h5 class="txt-dark">Lista Revista</h5>
					</div>
					<!-- Breadcrumb -->
					<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
					  <ol class="breadcrumb">
						<li><a href="<?=$_SESSION ['pathWeb']?>admin.php">Principal</a></li>
						<li><a href="<?=$_SESSION ['pathWeb']?>revista-lista.php"><span>Revista</span></a></li>
						<li class="active"><span>Lista Revista</span></li>
					  </ol>
					</div>
					<!-- /Breadcrumb -->
				</div>
				<!-- /Title -->
                <?php include_once ('alerts.php'); ?>
                <!-- Row -->
				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-default card-view">
							<div class="panel-heading">
								<div class="pull-left">
									<h6 class="panel-title txt-dark">Datos de la Tabla</h6>
								</div>
								<div class="pull-right">
									<a class="button" href="revista-nuevo.php">+ A�adir Revista</a>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="panel-wrapper collapse in">
								<div class="panel-body">
									<div class="table-wrap">
										<div class="">
											<table id="myTable1" class="table table-hover display  pb-30" >
												<thead>
													<tr>
														<th>T&iacute;tulo</th>
														<th>Periodo</th>
                                                        <th>A&ntilde;o</th>
                                                        <th>Fecha de creaci&oacute;n</th>
                                                        <th>Editar</th>
                                                        <th>Eliminar</th>
													</tr>
												</thead>
												<?php 
												if(count($registros) > 0){
												?>												
												<tfoot>
													<tr>
														<th>T&iacute;tulo</th>
														<th>Periodo</th>
                                                        <th>A&ntilde;o</th>
                                                        <th>Fecha de creaci&oacute;n</th>
                                                        <th>Editar</th>
                                                        <th>Eliminar</th>													</tr>
												</tfoot>
												<?php 
												}
												?>				
												<tbody>
												<?php 
												$contador = 1;
												if(count($registros) > 0){
													foreach($registros as $reg){
														echo '<tr class="renglon'.$reg['idevento'].'">
																<td>'.$reg['titulo'].'</td>
																<td>'.$reg['periodo'].'</td>
																<td>'.$reg['anio'].'</td>
																<td>'.$reg['fecha'].'</td>
																<td>
																	<a href="'.$_SESSION['pathWeb'].'revista-editar.php?id='.$reg['idrevista'].'&'.$db->url().'" id="m-'.$reg['idrevista'].'" class="editar">
																		<img src="'.$path_lib.'dist/img/icons/editar.png">
																	</a>
																</td>
																<td>
																	<a href="#" id="e-'.$reg['idrevista'].'-8" class="eliminar">
																		<img src="'.$path_lib.'dist/img/sweetalert/eliminar.png"																	
																		alt="alert" class="img-responsive model_img" id="sa-warning">
    																</a>
																</td>
															</tr>';
														$contador++;
													}
												}
												?>												
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>	
					</div>
				</div>
				<!-- /Row -->
			</div>
		<?php 
		include_once($_SESSION ['pathSys']."footer.php");
		?>
		</div>
		<!-- /Main Content -->
    </div>
<?php 
include_once($_SESSION ['pathSys']."libreriasAdmin.php");
$_SESSION['msgSuccess'] = $_SESSION['msgError'] = "";
?>