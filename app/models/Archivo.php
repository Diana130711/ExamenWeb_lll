<?php
	class Archivo {
		private $db;

		public function __construct() {
			$this->db = new Database();
		}

		// Archivos de una carpeta (sin el contenido, para no cargar todo en memoria)
		public function getByCarpeta($carpetaId) {
			$this->db->query('SELECT id, nombre, comentario, tipo, mime, peso_bytes, fecha_subida, propietario_id
			                  FROM archivos
			                  WHERE carpeta_id = :c
			                  ORDER BY nombre');
			$this->db->bind(':c', (int)$carpetaId);
			return $this->db->resultSet();
		}

		// Un archivo completo, con su contenido, para abrirlo o descargarlo
		public function getById($id) {
			$this->db->query('SELECT * FROM archivos WHERE id = :id');
			$this->db->bind(':id', (int)$id);
			return $this->db->singleRow();
		}

		// Guarda un archivo nuevo en la base de datos
		public function add($data) {
			$this->db->query('INSERT INTO archivos (nombre, comentario, tipo, mime, peso_bytes, contenido, carpeta_id, propietario_id)
			                  VALUES (:nombre, :comentario, :tipo, :mime, :peso, :contenido, :carpeta, :propietario)');
			$this->db->bind(':nombre', $data['nombre']);
			$this->db->bind(':comentario', $data['comentario']);
			$this->db->bind(':tipo', $data['tipo']);
			$this->db->bind(':mime', $data['mime']);
			$this->db->bind(':peso', (int)$data['peso']);
			$this->db->bind(':contenido', $data['contenido']);
			$this->db->bind(':carpeta', (int)$data['carpeta_id']);
			$this->db->bind(':propietario', (int)$data['propietario_id']);
			return $this->db->execute();
		}

		// Borra un archivo solo si pertenece al usuario
		public function delete($id, $usuarioId) {
			$this->db->query('DELETE FROM archivos WHERE id = :id AND propietario_id = :u');
			$this->db->bind(':id', (int)$id);
			$this->db->bind(':u', (int)$usuarioId);
			return $this->db->execute();
		}
	}
?>