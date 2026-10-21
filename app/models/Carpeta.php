<?php
	class Carpeta{
		private $db;

		public function __construct(){
			$this->db = new Database;
		}

		// crea la carpeta raíz de un usuario recién registrado
		public function crearCarpetaRaiz($usuarioId){
			$this->db->query('insert into carpetas (nombre, propietario_id, carpeta_padre_id) values (:nombre, :propietario_id, NULL)');
			$this->db->bind(':nombre', 'raiz');
			$this->db->bind(':propietario_id', $usuarioId);
			return $this->db->execute();
		}

		// trae la carpeta raíz de un usuario
		public function getRaizByUsuario($usuarioId){
			$this->db->query('select * from carpetas where propietario_id = :propietario_id and carpeta_padre_id is null');
			$this->db->bind(':propietario_id', $usuarioId);
			return $this->db->singleRow();
		}

		// trae una carpeta por su id
		public function getCarpetaById($id){
			$this->db->query('select * from carpetas where id = :id');
			$this->db->bind(':id', $id);
			return $this->db->singleRow();
		}

		// trae las subcarpetas directas, marcando si están compartidas
		public function getSubcarpetas($carpetaPadreId, $usuarioActualId){
			$this->db->query(
				'select c.*,
						exists(select 1 from carpetas_compartidas cc where cc.carpeta_id = c.id) as compartida,
						exists(select 1 from carpetas_compartidas cc2 where cc2.carpeta_id = c.id and cc2.compartido_con = :usuario_actual) as compartidaConmigo
				 from carpetas c
				 where c.carpeta_padre_id = :carpeta_padre_id
				 order by c.nombre'
			);
			$this->db->bind(':carpeta_padre_id', $carpetaPadreId);
			$this->db->bind(':usuario_actual', $usuarioActualId);
			return $this->db->resultSet();
		}

		// crea una subcarpeta nueva
		public function crearCarpeta($nombre, $carpetaPadreId, $usuarioId){
			$this->db->query('insert into carpetas (nombre, propietario_id, carpeta_padre_id) values (:nombre, :propietario_id, :carpeta_padre_id)');
			$this->db->bind(':nombre', $nombre);
			$this->db->bind(':propietario_id', $usuarioId);
			$this->db->bind(':carpeta_padre_id', $carpetaPadreId);
			return $this->db->execute();
		}

		// comparte una carpeta con otro usuario (evita duplicados)
		public function compartirCarpeta($carpetaId, $usuarioDestinoId, $usuarioActualId){
			if($this->estaCompartidaCon($carpetaId, $usuarioDestinoId)){
				return true;
			}
			$this->db->query('insert into carpetas_compartidas (carpeta_id, compartido_por, compartido_con) values (:carpeta_id, :compartido_por, :compartido_con)');
			$this->db->bind(':carpeta_id', $carpetaId);
			$this->db->bind(':compartido_por', $usuarioActualId);
			$this->db->bind(':compartido_con', $usuarioDestinoId);
			return $this->db->execute();
		}

		// indica si una carpeta está compartida con un usuario puntual
		public function estaCompartidaCon($carpetaId, $usuarioId){
			$this->db->query('select 1 from carpetas_compartidas where carpeta_id = :carpeta_id and compartido_con = :usuario_id');
			$this->db->bind(':carpeta_id', $carpetaId);
			$this->db->bind(':usuario_id', $usuarioId);
			return $this->db->rowCount() > 0;
		}

		// borra una carpeta propia (nunca la raíz, nunca de otro usuario)
		public function borrarCarpeta($id, $usuarioId){
			$this->db->query('delete from carpetas where id = :id and propietario_id = :propietario_id and carpeta_padre_id is not null');
			$this->db->bind(':id', $id);
			$this->db->bind(':propietario_id', $usuarioId);
			return $this->db->execute();
		}
	}
?>