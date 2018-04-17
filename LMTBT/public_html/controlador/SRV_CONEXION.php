<?php

class SRV_CONEXION {

	public $query = NULL;
	private $conexion;
	private $resultado;
	private $sqlHost;
	private $sqlDatabase;
	private $sqlUser;
	private $sqlPassword;
	// Recibe la consulta a ejecutar y la asigna a la variable local
	public function setQuery($value) {
		$this->query = $value;
	}
	// El constructor inicializa las variables para la conexión a la base de datos
	function __construct() {
		$this->sqlHost = "localhost";
		$this->sqlDatabase = "lmtbt";                                       		                                                                         		                                                                               		                                                                         		                                                                                                                                                                                                                                               
		$this->sqlUser = "root"; 
		$this->sqlPassword = "12345";                                                                                                       
	}
	// Realiza la conexion a la basee de datos
	public function DatabaseConnect() {
		$this->conexion = new mysqli($this->sqlHost, $this->sqlUser, $this->sqlPassword, $this->sqlDatabase);
		mysqli_set_charset($this->conexion, "utf8");
		if (mysqli_connect_errno()) {
			echo "conexion fallida:", mysqli_connect_errno();
			exit();
		}
	}

	//Ejecuta el query establecido con 'setQuery' y devuelve un booleano indicando si la consulta fue exitosa.
	public function ExecuteQuery() {
		if (!$this->conexion)
			$this->DatabaseConnect();

		$this->resultado = $this->conexion->query($this->query);

		return $this->resultado != false;
	}
	// Retorna un arreglo con los datos, retorna un arreglo de filas asociadas a la consulta
	public function GetResult() {
		$res = array();
		$this->ExecuteQuery();
		while ($row = $this->resultado->fetch_assoc()) {
			$res[] = $row;
		}
		return $res;
	}
	// Retorna un arreglo con los datos de un solo usuario, por ejemplo al editar los datos de un usuario específico, retorna la fila de la tabla correspondiente a ese usuario
	public function GetRow() {
		$res = array();
		$this->ExecuteQuery();
		$res = $this->resultado->fetch_assoc();
		return $res;
	}
	// Metodo que retorna la conexion a la base de datos, se auxilia del metodo DatabaseConnect() que es el encargado de realizar la conexion a la base de datos
	public function getConnection() {
		if (!$this->conexion)
			$this->DatabaseConnect();

		return $this->conexion;
	}
	// Metodo para cerrar la conexión a la base de datos
	public function close() {
		if ($this->conexion)
			$this->conexion->close();
	}

}

?>
