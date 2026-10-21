<?php
	class Carpetas extends Controller {

		private $carpetaModel;
		private $userModel;

		public function __construct(){
			if(!isLoggedIn()){
				header('location: ' . urlRoot . '/users/login');
				exit();
			}
			$this->carpetaModel = $this->model('Carpeta');
			$this->userModel = $this->model('User');
		}

		public function index($carpetaId = null){
			$usuarioId = $_SESSION['usuario_id'];

			if($carpetaId === null){
				$carpetaActual = $this->carpetaModel->getRaizByUsuario($usuarioId);
			}else{
				$carpetaActual = $this->carpetaModel->getCarpetaById((int)$carpetaId);
			}

			if(!$carpetaActual){
				die('La carpeta solicitada no existe');
			}

			$esDueno = $carpetaActual->propietario_id == $usuarioId;
			$esCompartida = $this->carpetaModel->estaCompartidaCon($carpetaActual->id, $usuarioId);

			if(!$esDueno && !$esCompartida){
				die('No tenés permiso para ver esta carpeta');
			}

			$data = [
				'carpetaActual' => $carpetaActual,
				'subcarpetas'   => $this->carpetaModel->getSubcarpetas($carpetaActual->id, $usuarioId),
				'archivos'      => [], // Persona 2 (Diana) lo completa con su modelo Archivo
				'usuarios'      => $this->userModel->getOtrosUsuarios($usuarioId)
			];

			$this->view('carpetas/index', $data);
		}

		public function crear($carpetaPadreId){
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$txtNombre = filter_input(INPUT_POST, 'txtNombre', FILTER_DEFAULT);
				$nombre = $txtNombre !== null ? trim($txtNombre) : '';

				if(!empty($nombre)){
					$this->carpetaModel->crearCarpeta($nombre, (int)$carpetaPadreId, $_SESSION['usuario_id']);
				}
			}
			header('location: ' . urlRoot . '/carpetas/index/' . $carpetaPadreId);
			exit();
		}

		public function compartir($carpetaId){
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$usuarioDestino = filter_input(INPUT_POST, 'txtUsuarioDestino', FILTER_VALIDATE_INT);

				if($usuarioDestino){
					$this->carpetaModel->compartirCarpeta((int)$carpetaId, $usuarioDestino, $_SESSION['usuario_id']);
				}
			}
			header('location: ' . urlRoot . '/carpetas/index/' . $carpetaId);
			exit();
		}

		public function borrar($carpetaId){
			$carpeta = $this->carpetaModel->getCarpetaById((int)$carpetaId);
			$carpetaPadreId = $carpeta ? $carpeta->carpeta_padre_id : null;

			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$this->carpetaModel->borrarCarpeta((int)$carpetaId, $_SESSION['usuario_id']);
			}

			header('location: ' . urlRoot . '/carpetas/index/' . $carpetaPadreId);
			exit();
		}
	}
?>