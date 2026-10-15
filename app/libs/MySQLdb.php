<?php  
/**
 * 
 */
class MySQLdb
{
	private $host="localhost";
	private $usuario = "root";
	private $clave = "";
	private $db = "biblioteca";
	private $puerto = "";
	private $conn;
	
	function __construct()
	{
		try {
		  $this->conn = new PDO(
		  	'mysql:host='.$this->host.';dbname='.$this->db, 
		  	$this->usuario, 
		  	$this->clave
		  );
		  echo "Conexión a la base de datos: ¡Conectado correctamente!_sistemas de información_22_09_2026";
		} catch (Exception $e) {
		  die("No se pudo conectar: " . $e->getMessage());
		}
	}
}
?>