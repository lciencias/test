<?php
include_once ("Comunes.class.php");
class Categoria extends Comunes{

	private $db;
	public $session;
	private $data;
	private $idImagen;
	private $opc;
	private $mensaje;
	private $exito;
	private $registros;
	private $tabla;
	private $buffer;
	private $total;
	
	function __construct($db,$session,$data,$idImagen,$opc){
		parent::__construct($session);
		$this->db 		   = $db;
		$this->session     = $session;
		$this->data        = $data;
		$this->idImagen    = $idImagen;
		$this->opc         = $opc;
		$this->mensaje     = "";
		$this->buffer      = "";
		$this->tabla       = "categorias";
		$this->exito       = Comunes::LISTAR;
		$this->total       = 0;
		$this->registros   = array();
		switch($this->opc){
			case Comunes::LISTAR:
				$this->listarCategoria();
				break;
			case Comunes::SAVE:
				$this->guardaCategoria();
				break;
			case Comunes::EDIT:
				$this->totalCategoria();
				$this->editaCategoria();
				break;
			case Comunes::UPDATE:
				$this->actualizaCategoria();
				break;
			case Comunes::DELETE:
				$this->eliminaCategoria();
				break;	
			case Comunes::ORDENAR:
				$this->ordenaRegstro();
				break;
		}
	}
	
	private function listarCategoria(){
		$this->registros = array();
		try{
			$sql = "SELECT a.id,a.nombre,
					DATE_FORMAT(a.fecha,'%d-%m-%Y %H:%i') as fecha,
					a.status,a.orden
					FROM ".$this->tabla." as a 
					WHERE a.status= ".Comunes::SAVE." ORDER BY a.nombre;";
			$res = $this->db->sql_query ($sql);			
			if ($this->db->sql_numrows ($res) > 0){
				$this->total = $this->db->sql_numrows ($res);
				while($row = $this->db->sql_fetchass($res)){
					$this->registros[] = $row;
				}
			}
			$this->total++;
		}catch (\Exception $e){
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}		
	}
	
	private function totalCategoria(){
		try{
			$sql = "SELECT a.id,a.nombre,
					DATE_FORMAT(a.fecha,'%d-%m-%Y %H:%i') as fecha,
					a.status,a.orden
					FROM ".$this->tabla." as a 
					WHERE a.status= ".Comunes::SAVE." ORDER BY a.id;";
			$res = $this->db->sql_query ($sql);
			$this->total = $this->db->sql_numrows ($res);			
		}catch (\Exception $e){
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}				
	}
	
	private function guardaCategoria(){
		$fecha = date("Y-m-d H:i:s");
		try{
			$this->mensaje = "Los datos de la categoria no se cargaron correctamente";
			if(count($this->data) > 0){			
				foreach($this->data as $key => $value){
					$this->data[$key] = $this->eliminaCaracteresInvalidos($value);
				}
				$ins = "INSERT INTO ".$this->tabla."(nombre,fecha,status, orden)
						VALUES ('".$this->data['nombre']."','".$fecha."','1','".$this->data['orden']."');";				
				$this->db->sql_query($ins);
				$this->mensaje = Comunes::MSGSUCESS;
				$this->exito   = Comunes::SAVE;
			}
		}
		catch(\Exception $e){
			$this->mensaje = Comunes::MSGERROR;
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}	
	}
	
	private function editaCategoria(){
		$this->exito = -1;
		$id = (int)$this->data['id'];
		try{
			if($id > 0){
				$this->exito = 1;
				$sql = "SELECT a.id,a.nombre,
						DATE_FORMAT(a.fecha,'%d-%m-%Y %H:%i') as fecha,
						a.status,a.orden 
						FROM ".$this->tabla." as a  
						WHERE a.id = '".$id."' LIMIT 1;";
				$res = $this->db->sql_query ($sql);
				if ($this->db->sql_numrows ($res) > 0){
					$this->registros = $this->db->sql_fetchass($res);
				}			
			}
		}
		catch(\Exception $e){
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}		
	}
	
	
	private function actualizaCategoria(){
		$fecha = date("Y-m-d H:i:s");
		try{
			$this->mensaje = "Los datos de la categoria no se alamcenaron correctamente";
			if(count($this->data) > 0){
				foreach($this->data as $key => $value){
					$this->data[$key] = $this->eliminaCaracteresInvalidos($value);
				}
				$ins = "UPDATE ".$this->tabla." set ";
				$ins .= "nombre = '".$this->data['nombre']."',
						 orden  = '".$this->data['orden']."',
						 fecha  = '".$fecha."',
						 status = '". Comunes::SAVE."'
						 WHERE id = '".$this->data['id']."' limit 1;";
				$this->db->sql_query($ins);
				$this->mensaje = Comunes::MSGSUCESS;
				$this->exito   = 1;
			}
		}
		catch(\Exception $e){
			$this->mensaje = Comunes::MSGERROR;
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}		
	}
	
	
	private function eliminaCategoria(){
		$this->exito   = Comunes::LISTAR;
		$this->mensaje = Comunes::ERROR; 
		if((int) $this->idImagen > 0){
			try{
				$upd = "UPDATE ".$this->tabla." SET status = '". Comunes::EDIT."' WHERE id= '".$this->idImagen."' LIMIT 1;";
				$this->db->sql_query($upd);
				$this->exito = Comunes::SAVE;
				$this->mensaje = Comunes::MSGSUCESS;
			}catch(\Exception $e){
				$this->mensaje = $e->getMessage();
				$this->writeLog($e->getMessage(), Comunes::ERROR);
			}
		}
	}
	
	private function ordenaRegstro(){
		$id = (int) $this->data['id'];
		$valor = (int) $this->data['valor'];
		if($id > 0 && $valor > 0){
			try{
				$upd = "UPDATE ".$this->tabla." SET orden = '".$valor."' WHERE id= '".$id."' LIMIT 1;";
				$this->db->sql_query($upd);
				$this->exito = Comunes::SAVE;
				$this->mensaje = Comunes::MSGSUCESS;
			}catch(\Exception $e){
				$this->mensaje = $e->getMessage();
				$this->writeLog($e->getMessage(), Comunes::ERROR);
			}
			
		}	
	}
	
	function obtenExito(){
		return $this->exito;
	}

	function obtenMensaje(){
		return $this->mensaje;
	}

	function obtenBuffer(){
		return $this->buffer;
	}
	
	function obtenRegistros(){
		return $this->registros;
	}
	function obtenTotalCategorias(){
		return $this->total;
	}
}
?>