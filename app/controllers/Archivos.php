<?php
	class Archivos extends Controller {

		private $archivoModel;

		// Límite de 20 MB en bytes
		const MAX_BYTES = 20 * 1024 * 1024;

		public function __construct() {
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
					$data['error'] = 'No se pudo recibir