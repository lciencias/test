<?php
include_once ("Comunes.class.php");
class Producto extends Comunes{

	private $db;
	public $session;
	private $idDocumento;
	private $data;
	private $idImagen;
	private $opc;
	private $mensaje;
	private $exito;
	private $registros;
	private $tabla;
	private $filtro;
	private $arrayCategorias;
	private $totalProductos;
	
	function __construct($db,$session,$data,$idImagen,$idDocumento,$opc){
		parent::__construct($session);
		$this->db 		   = $db;
		$this->session     = $session;
		$this->data        = $data;
		$this->idImagen    = $idImagen;
		$this->idDocumento = $idDocumento;
		$this->filtro      = "";
		$this->opc         = $opc;
		$this->mensaje     = "";
		$this->tabla       = "productos";
		$this->exito       = Comunes::LISTAR;
		$this->registros= array();
		$this->totalProductos = 0;		
		$this->arrayCategorias = array();

		switch($this->opc){
			case Comunes::LISTAR:
				$this->categorias();
				$this->listarProducto();
				break;
			case Comunes::SAVE:
				$this->categorias();
				$this->guardaProducto();
				break;
			case Comunes::EDIT:
				$this->totalProductos();
				$this->categorias();
				$this->editaProducto();
				break;
			case Comunes::UPDATE:
				$this->actualizaProducto();
				break;
			case Comunes::DELETE:
				$this->eliminaProducto();
				break;
			case Comunes::WEB:
				break;
			case Comunes::ORDENAR:
				$this->ordenaRegstro();
				break;
		}
	}
	
	private function totalProductos(){
		try{
			$sql = "SELECT a.id 
					FROM ".$this->tabla." as a 
					WHERE a.status= ".Comunes::SAVE.";";
			$res = $this->db->sql_query ($sql);
			$this->totalProductos = $this->db->sql_numrows ($res);			
		}catch (\Exception $e){
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}				
	}
	private function categorias(){
		$this->arrayCategorias = array();
		try{
			$sql = "SELECT a.id,a.nombre FROM categoria as a
					WHERE a.status = '".Comunes::SAVE."' ORDER BY a.nombre ASC;";
			$res = $this->db->sql_query ($sql);
			if ($this->db->sql_numrows ($res) > 0){
				while(list($id,$nombre) = $this->db->sql_fetchrow($res)){
					$this->arrayCategorias[$id] = $nombre;
				}
			}
		}catch (\Exception $e){
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}
		
	}
	private function filtros(){
		$this->filtro = "";
		if((int) $this->data['idanio'] > 0){
			$this->filtro = " AND a.anio = '".$this->data['idanio']."' ";
		}
	}
	
	
	private function listarProducto(){
		$this->registros = array();
		try{
			$sql = "SELECT a.id,a.idcategoria,a.producto,a.caloria,DATE_FORMAT(a.fecha, '%d-%m-%y %H:%i:%s') AS fecha,
					a.precio,a.status,a.idimagen,c.nombre as categoria,b.archivo,b.ruta,b.web,a.orden 					
					FROM ".$this->tabla." as a 
					LEFT JOIN imagen as b ON b.idimagen = a.idimagen
					LEFT JOIN categorias as c ON c.id = a.idcategoria					
					WHERE a.status = '".Comunes::SAVE."' ORDER BY a.id asc;";
			$res = $this->db->sql_query ($sql);			
			$this->totalProductos = $this->db->sql_numrows ($res);
			if ($this->db->sql_numrows ($res) > 0){
				while($row = $this->db->sql_fetchass($res)){
					$this->registros[] = $row;
				}				
			}
			$this->totalProductos++;
		}catch (\Exception $e){
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}		
	}

	private function guardaProducto(){
		$fecha = date("Y-m-d H:i:s");
		try{
			$this->mensaje = "Los datos del producto no se cargaron correctamente";
			if((int)$this->idImagen > 0){			
				foreach($this->data as $key => $value){
					$this->data[$key] = $this->eliminaCaracteresInvalidos($value);
				}
				$ins = "INSERT INTO ".$this->tabla."(idcategoria,producto,caloria,precio,fecha, status, idImagen)
						VALUES ('".$this->data['idcategoria']."','".$this->data['producto']."','".$this->data['caloria']."',
								'".$this->data['precio']."','".$fecha."','".Comunes::SAVE."','".$this->idImagen."');";
				$res = $this->db->sql_query($ins);
			}
		}
		catch(\Exception $e){
			$this->mensaje = Comunes::MSGERROR;
			$this->writeLog($e->getMessage(), Comunes::ERROR);
		}	
	}
	
	private function editaProducto(){
		$this->exito = -1;
		$id = (int)$this->data['id'];
		try{
			if($id > 0){
				$this->exito = 1;
				$sql = "SELECT a.id,a.idcategoria,a.producto,a.caloria,DATE_FORMAT(a.fecha, '%d-%m-%y %H:%i:%s') AS fecha,
					a.precio,a.status,a.idimagen,c.nombre as categoria,b.archivo,b.ruta,b.web as imagen,a.idimagen 					
					FROM ".$this->tabla." as a 
					LEFT JOIN imagen as b ON b.idimagen = a.idimagen
					LEFT JOIN categoria as c ON c.id = a.idcategoria	
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
	
	private function actualizaProducto(){
		$fecha = date("Y-m-d H:i:s");
		try{
			$this->mensaje = "Los datos del producto no se cargaron correctamente";
			if(count($this->data) > 0){
				foreach($this->data as $key => $value){
					$this->data[$key] = $this->eliminaCaracteresInvalidos($value);
				}
				$ins = "UPDATE ".$this->tabla." set ";
				if((int)$this->idImagen > 0){
					$ins .= "idimagen     ='".$this->idImagen."',";
				}
				$ins .= " idcategoria = '".$this->data['idcategoria']."',		
						  producto    = '".$this->data['producto']."',
						  caloria     = '".$this->data['caloria']."',
						  precio      = '".$this->data['precio']."',
						  orden      = '".$this->data['orden']."',
						  fecha       = '".$fecha."',
						  status      = '". Comunes::SAVE."'
						WHERE id      = '".$this->data['id']."';";
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
	
	private function eliminaProducto(){
		$this->exito   = Comunes::LISTAR;
		$this->mensaje = Comunes::ERROR; 
		if((int) $this->idImagen > 0){
			try{
				$upd = "UPDATE ".$this->tabla." SET status = '". Comunes::EDIT."' WHERE id = '".$this->idImagen."' LIMIT 1;";
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
	
	function obtenCategorias(){
		return $this->arrayCategorias;
	}
	
	function obtenExito(){
		return $this->exito;
	}

	function obtenMensaje(){
		return $this->mensaje;
	}
	
	function obtenRegistros(){
		return $this->registros;
	}

	function obtenTotalProductos(){
		return $this->totalProductos;
	}
}
?>