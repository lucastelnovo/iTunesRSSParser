<?php

class Parser {
	
	private $urlRssDoc; // Lista con los rss a convertir a formato iTunes
	private $elementsList; // Lista en forma de strings con elemementos obligatorios
	private $elementsMap; // Mapa con (elemento, array)
	private $defaultInformationMap; // Mapa con (elemento, valor_default)
	

	public function __construct($urlRssDoc, $elementsList, $defaultInformationMap) {
		
		$this->urlRssDoc = $urlRssDoc;
		$this->defaultInformationMap = $defaultInformationMap;
		$this->elementsList = $elementsList;
		$this->elementsMap = $this->createElementsMap ( $this->elementsList );
	
	}
	
	public function createElementsMap() {
		
		/*
		 * @INFO
		 * for each ($string en la lista elementsList)
		 *		agregame un elemento al mapa con key = $string y contenido con un array vacio.
		 */
		
		$keys = array(); // Defino un array en el que guardo los elementos que seran las keys del mapa
		
		foreach ( $this->elementsList as $string ) {

			$keys[] = $string; // Completo el array con todos los elementos poniendolos como keys
		
		}
		
		return array_fill_keys ( $keys, $array = array () ); // A cada key le doy de valor un array vacio
	
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