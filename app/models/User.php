<?php
	class User{
		private $db;
		
		//create database object
		public function __construct(){
			$this->db = new Database;
		}
		
		//retrive all user data
		public function getUsers(){
			$this->db->query('select * from usuarios');
			$regis = $this->db->resultSet();
			return $regis;
		}
		
		//retrieve an user data, loging function
		public function login($data){
			//create retrieve sentence
			$this->db->query('select * from usuarios where usuario = :nomb');
			
			//bind sentence with variable
			$this->db->bind(':nomb',$data['usuario']);
			
			//retrieve a single datarow
			$tupla = $this->db->singleRow();
			
			//si el usuario no existe, no hay nada que validar
			if(!$tupla){ return false; }
			
			//update local variable with contra field from single row 
			$contra = $tupla->contra;        
			
			//validate passwords (retrieved pass with param pass)
			if(password_verify($data['contra'], $contra)){
				return $tupla;    
			}else{
				return false;
			}
		}
		
		// register new user function
		public function register($data){
			//create insert sentence
			$this->db->query('insert into usuarios(usuario,contra,nombre,email) values(:usua,:cont,:nomb,:email)');
			
			//bind sentence with variable
			$this->db->bind(':usua',$data['usuario']);
			$this->db->bind(':cont',$data['contra']);
			$this->db->bind(':nomb',$data['nombre']);
			$this->db->bind(':email',$data['email']);
			
			//execute SQL sentence
			if($this->db->execute()){
				return $this->db->lastInsertId();
			}else{
				return false;
			}                            
		}//end register user function

		//Modificacion: recupera un usuario completo por su nombre de usuario
		public function getUserByUsuario($usuario){
			$this->db->query('select * from usuarios where usuario = :usua');
			$this->db->bind(':usua', $usuario);
			return $this->db->singleRow();
		}

	    //trae todos los usuarios excepto el actual, para el combo de compartir carpeta
		public function getOtrosUsuarios($usuarioActualId){
			$this->db->query('select id, nombre from usuarios where id != :id order by nombre');
			$this->db->bind(':id', $usuarioActualId);
			return $this->db->resultSet();
		}
	}
?>