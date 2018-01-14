<?php

class SRV_CONEXION {

	public $query = NULL;
	private $conexion;
	private $resultado;
	private $sqlHost;
	private $sqlDatabase;
	private $sqlUser;
	private $sqlPassword;

	public function setQuery($value) {
		$this->query = $value;
	}

	function __construct() {
		$this->sqlHost = "localhost";
		$this->sqlDatabase = "lmtbt";
		$this->sqlUser = "tochomaster"; 
		$this->sqlPassword = "tochoweb";                                         		                                         
	}

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

	public function GetResult() {
		$res = array();
		$this->ExecuteQuery();
		while ($row = $this->resultado->fetch_assoc()) {
			$res[] = $row;
		}
		return $res;
	}

	public function GetRow() {
		$res = array();
		$this->ExecuteQuery();
		$res = $this->resultado->fetch_assoc();
		return $res;
	}

	public function getConnection() {
		if (!$this->conexion)
			$this->DatabaseConnect();

		return $this->conexion;
	}

	public function close() {
		if ($this->conexion)
			$this->conexion->close();
	}

}

?>
