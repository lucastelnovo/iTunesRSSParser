<?php

class Parser {
	
	private $urlRssDoc; // Lista con los rss a convertir a formato iTunes
	private $elementsMap; // Lista en forma de strings con elemementos obligatorios
	private $defaultInformationMap; // Mapa con (elemento, valor_default)
	

	public function __construct($urlRssDoc, $elementsList, $defaultInformationMap) {
		
		$this->urlRssDoc = $urlRssDoc;
		$this->defaultInformationMap = $defaultInformationMap;
		
		$this->createElementsMap ( $elementsList );
	
	}
	
	public function createElementsMap($elementsList) {
		/*
		 * for each ($string en la lista elementsList)
		 *		agregame un elemento al mapa con key = $string y contenido con un array vacio.
		 */
	
	}
	
	// Para cada documento extrae la descripcion/contenido de los elementos 
	public function extractElements() {
		
		/*
		 * 
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