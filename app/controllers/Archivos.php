<?php
	class Archivos extends Controller {

		private $archivoModel;

		// Límite de 20 MB en bytes
		const MAX_BYTES = 20 * 1024 * 1024;

		public function __construct() {
			// TEMPORAL: simula sesión con el usuario de prueba (id 1).
			// Borrar esta línea cuando se integre el login de la compañera.
			$_SESSION['usuario_id'] = $_SESSION['usuario_id'] ?? 1;

			// Solo usuarios autenticados
			if (!isset($_SESSION['usuario_id'])) {
				header('Location: ' . urlRoot . '/users/login');
				exit();
			}
			$this->archivoModel = $this->model('Archivo');
		}

		// Subir un archivo a una carpeta
		public function upload($carpetaId = null) {
			$usuarioId = (int)$_SESSION['usuario_id'];

			// La carpeta debe existir y ser del usuario
			if (!$carpetaId || !$this->archivoModel->carpetaEsDelUsuario($carpetaId, $usuarioId)) {
				header('Location: ' . urlRoot . '/carpetas');
				exit();
			}

			$data = [
				'carpeta_id' => (int)$carpetaId,
				'comentario' => '',
				'error'      => ''
			];

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$data['comentario'] = mb_substr(trim($_POST['txtComentario'] ?? ''), 0, 50);
				$archivo = $_FILES['txtArchi'] ?? null;

				// Validaciones
				if (!$archivo || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
					$data['error'] = 'Seleccione un archivo.';
				} elseif ($archivo['error'] === UPLOAD_ERR_INI_SIZE || $archivo['error'] === UPLOAD_ERR_FORM_SIZE) {
					$data['error'] = 'El archivo supera el máximo permitido de 20 MB.';
				} elseif ($archivo['error'] !== UPLOAD_ERR_OK) {
					$data['error'] = 'No se pudo recibir el archivo. Intente de nuevo.';
				} elseif ($archivo['size'] > self::MAX_BYTES) {
					$data['error'] = 'El archivo supera el máximo permitido de 20 MB.';
				}

				if ($data['error'] === '') {
					$nombre = basename($archivo['name']);

					$nuevo = [
						'nombre'         => $nombre,
						'comentario'     => $data['comentario'],
						'tipo'           => $this->calcularTipo($nombre),
						'mime'           => mime_content_type($archivo['tmp_name']) ?: 'application/octet-stream',
						'peso'           => $archivo['size'],
						'contenido'      => file_get_contents($archivo['tmp_name']),
						'carpeta_id'     => (int)$carpetaId,
						'propietario_id' => $usuarioId
					];

					if ($this->archivoModel->add($nuevo)) {
						header('Location: ' . urlRoot . '/carpetas/index/' . (int)$carpetaId);
						exit();
					}
					$data['error'] = 'No se pudo guardar el archivo en la base de datos.';
				}
			}

			$this->view('archivos/upload', $data);
		}

		// Abrir: PDF e imágenes se muestran; lo demás se descarga
		public function open($id = null) {
			$archivo = $id ? $this->archivoModel->getById($id) : false;

			// Por ahora solo el dueño. Al integrar se agrega "o compartido conmigo".
			if (!$archivo || (int)$archivo->propietario_id !== (int)$_SESSION['usuario_id']) {
				header('Location: ' . urlRoot . '/carpetas');
				exit();
			}

			$mime = $archivo->mime;
			$nombre = str_replace('"', '', $archivo->nombre);
			$mostrar = ($mime === 'application/pdf' || str_starts_with($mime, 'image/'));

			header('Content-Type: ' . $mime);
			header('Content-Length: ' . $archivo->peso_bytes);
			if ($mostrar) {
				header('Content-Disposition: inline; filename="' . $nombre . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $nombre . '"');
			}
			echo $archivo->contenido;
			exit();
		}

		// Borrar un archivo (solo por POST y solo si es del usuario)
		public function delete($id = null) {
			$archivo = $id ? $this->archivoModel->getById($id) : false;
			$carpetaId = $archivo ? $archivo->carpeta_id : '';

			if ($_SERVER['REQUEST_METHOD'] === 'POST' && $archivo) {
				$this->archivoModel->delete($id, $_SESSION['usuario_id']);
			}
			header('Location: ' . urlRoot . '/carpetas/index/' . $carpetaId);
			exit();
		}

		// Calcula el tipo de documento a partir de la extensión
		private function calcularTipo($nombre) {
			$ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
			return match ($ext) {
				'pdf'                                 => 'PDF',
				'doc', 'docx', 'odt'                  => 'Word',
				'xls', 'xlsx', 'ods', 'csv'           => 'Excel',
				'ppt', 'pptx', 'odp'                  => 'PowerPoint',
				'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg' => 'Imagen',
				'txt', 'md'                           => 'Texto',
				'zip', 'rar', '7z'                    => 'Comprimido',
				'mp3', 'wav'                          => 'Audio',
				'mp4', 'avi', 'mov'                   => 'Video',
				default                               => 'Otro'
			};
		}
	}
?>