<?php

class Parser {
	
	private $urlsRss; // Lista con los rss a convertir a formato iTunes
	private $elementsList; // Lista en forma de strings con elemementos obligatorios
	public $elementsMap; // Mapa con (elemento, array)
	private $defaultInformationMap; // Mapa con (elemento, valor_default)
	

	public function __construct($urlsRss, $elementsList, $defaultInformationMap) {
		
		$this->$urlsRss = $urlsRss;
		$this->defaultInformationMap = $defaultInformationMap;
		$this->elementsList = $elementsList;
		$this->elementsMap = $this->createElementsMap ();
	
	}
	
	public function createElementsMap() {
		
		/*
		 * @INFO
		 * for each ($string en la lista elementsList)
		 *		agregame un elemento al mapa con key = $string y contenido con un array vacio.
		 */
		
		$keys = array(); // Defino un array en el que guardo los elementos que seran las keys del mapa
		
		foreach ( $this->elementsList as $string ) {
			
			$emptyArray = array ();
			$keys[$string] = $emptyArray; // Completo el array con todos los elementos poniendolos como keys
		
		}
		
		return $keys;
	}
	
	
	// Para cada documento extrae la descripcion/contenido de los elementos 
	public function extractElements() {
		
		/*
		 * @INFO
		 * foreach($elemento de la lista de elementos)
		 * 		$miElemento = $rss->channel->$elemento
		 * 		foreach($miElemento as $descripcionDeElementoActual)
		 * 			addToList($elemento->listaDeContenidos, $descripcionDeElementoActual)
		 * 
		 */
		
		$rss = simplexml_load_file ( $this->urlRssDoc );
		
		if ($rss) {
			echo '<h1>' . $rss->channel->title . '</h1>';
			echo '<li>' . $rss->channel->link . '</li>';
			$items = $rss->channel->item;
		
		}
	
	}

}

?>