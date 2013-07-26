<?php

class Auxiliar {
	
	private $path;
	
	public function __construct($path) {
		
		$this->path = $path;
	}
	
	public function devolverXML($unXML) {
		
		if (file_exists ( $this->path ))
			unlink($this->path);
		
		$handle = fopen ( $this->path, "x" );
		fwrite ( $handle, $unXML );	
	}

}

?>