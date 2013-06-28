<?php

class Parser {
	
	private $urlsRss; // Lista con los rss a convertir a formato iTunes
	private $elementsList; // Lista en forma de strings con elemementos obligatorios
	public $elementsMap; // Mapa con (elemento, array)
	private $defaultInformationMap; // Mapa con (elemento, valor_default)
	

	public function __construct($urlsRss, $elementsList, $defaultInformationMap) {
		
		$this->urlsRss = $urlsRss;
		$this->defaultInformationMap = $defaultInformationMap;
		$this->elementsList = $elementsList;
		$this->elementsMap = $this->createElementsMap ();
	
	}
	
	public function createElementsMap() {
		/*
		 * Crea el mapa de elementos, con key = element y contenido = array vacío.
		 */
		
		$keys = array (); // Defino un array en el que guardo los elementos que seran las keys del mapa
		

		foreach ( $this->elementsList as $string ) {
			$emptyArray = array ();
			$keys [$string] = $emptyArray; // Completo el array con todos los elementos poniendolos como keys
		}
		
		return $keys;
	}
	
	public function createValidFeed() {
		//escupe los headers al nuevo rss
		$this->newFeed .= "<rss xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:dcterms=\"http://purl.org/dc/terms/\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" version=\"2.0\">" . "\n";
		
		//foreach archivo pasado a parsear
		foreach ( $urlsRss as $url ) {
			
			//abre el channel
			$this->newFeed .= "<channel>";
			
			//extrae la info al mapa
			$this->extractElementsContent ( $url );
			
			//escupe la info del mapa al nuevo rss
			$this->addElementsContent ();
			
			//limpia el mapa
			foreach ( array_keys ( $this->elementsMap ) as $key ) {
				//TODO
			}
			
			//cierra el channel
			$newFeed .= "</channel>";
			
			//returnea el nuevo feed
			return $newFeed;
		}
	}
	
	public function extractElementsContent($url) {
		// Para cada documento extrae la descripcion/contenido de los elementos 
		

		//foreach elemento de la lista
		foreach ( $this->elementsMap as $element ) {
		
		}
	
		//busca el element del mapa en el archivo y agregá su contenido al array del mapa
	

	//
	

	/*
			$rss = simplexml_load_file($url);
			if($rss)
				{
				echo '<h1>'.$rss->channel->title.'</h1>';
				echo '<li>'.$rss->channel->pubDate.'</li>';
				$items = $rss->channel->item;
				foreach($items as $item)
				{
			$title = $item->title;
			$link = $item->link;
			$published_on = $item->pubDate;
			$description = $item->description;
			echo '<h3><a href="'.$link.'">'.$title.'</a></h3>';
			echo '<span>('.$published_on.')</span>';
			echo '<p>'.$description.'</p>';
			}
	*/
	}

	public function addElementsContent(){
		
	}

}

?>