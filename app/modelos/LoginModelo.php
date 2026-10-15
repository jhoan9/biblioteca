<?php  
/**
 * 
 */
class LoginModelo
{
	private $db = "";
	
	function __construct()
	{
		$this->db = new MySQLdb();
	}
}

?>