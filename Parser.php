<?php

include_once 'API FeedWriter/FeedWriter.php';

class Parser {
	
	private $urlsRss; // Lista con los rss a convertir a formato iTunes
	private $elementsList; // Lista en forma de strings con elemementos obligatorios
	private $elementsMap; // Mapa con (elemento, array con la info del elemento (contenidos, hijos))
	private $defaultInformationMap; // Mapa con (elemento, valor_default)
	

	public function __construct($urlsRss, $elementsList, $defaultInformationMap) {
		
		$this->urlsRss = $urlsRss;
		$this->defaultInformationMap = $defaultInformationMap;
		$this->elementsList = $elementsList;
		$this->elementsMap = $this->createElementsMap ();
	
	}
	
	public function createElementsMap() {
		/* TODO Ver c�mo pasar el array base, y crear bien los nombres de las keys y los hijos.
		 * Crea el mapa de elementos, con key = element, elementInfo = array de dos arrays: uno con el contenido del elemento, y otro con los hijos, ambos inicializados vac�os.
		 */
		
		// Defino un array en el que guardo los elementos que seran las keys del mapa
		$elements = array ();
		
		// Para cada elemento de la lista de elementos, le asigno el nombre del elemento como key, y de value le asigna un array de dos keys (de nombres fijos "contents" y "sons", donde cada una tiene un array vac�o.
		foreach ( $this->elementsList as $element ) {
			// asigno el nombre de la key seg�n el element de la lista de elementos
			/*HERE $elements[$element] = $elementsInfo = [$content[], $sons[]];*/
			
			//
			
			
			
			
			/* Apunta todo al mismo array?
			 * $emptyArray = array ();
			 * $keys [$string] = $emptyArray;
			*/
		}
		
		return $keys;
	}
	
	public function createValidFeed() {
		//escupe los headers al nuevo rss
		$this->newFeed .= "<rss xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:dcterms=\"http://purl.org/dc/terms/\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" version=\"2.0\">" . "\n";
		
		//foreach archivo pasado a parsear
		foreach ( $this->urlsRss as $url ) {
			
			//abre el channel
			$this->newFeed .= "<channel>";
			
			//extrae la info al mapa
			$this->extractElementsContent ( $url );
			
			//escupe la info del mapa al nuevo rss
			$this->addElementsContent ();
			
			//limpia el mapa
			erase_values ( $this->elementsMap );
			
			/*
			 * Si no quiero usar la funci�n privada de arriba (porque puede quedar fea una auxiliar no est�tica), uso este foreach de abajo.
			 *
			 * REF: http://php.net/manual/es/control-structures.foreach.php, http://stackoverflow.com/questions/2217160/delete-all-values-from-an-array-while-keeping-keys-intact, y esto es lindo http://stackoverflow.com/questions/9568044/php-remove-empty-null-array-key-values-while-keeping-key-values-otherwise-not-e
			 *
			 * foreach ( $this->elementsMap as $i => $value ) {
			 *	unset ( $array [$i] );
			 * }
			*/
			
			//cierra el channel
			$newFeed .= "</channel>";
			
			//returnea el nuevo feed
			return $newFeed;
		}
	}
	
	private function erase_values(&$myarr) {
		/*
		 * Borra las values de un mapa, pero conserva las keys. Funci�n gen�rica.
		 */
		$myarr = array_map ( create_function ( '$n', 'return null;' ), $myarr );
	}
	
	public function extractElementsContent($url) {
		// Para cada documento extrae la descripcion/contenido de los elementos 
		

		//foreach elemento de la lista
		foreach ( $this->elementsMap as $element ) {
		
		}
	
		//busca el element del mapa en el archivo y agreg� su contenido al array del mapa
	

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
	
	public function addElementsContent() {
		
		/* @INFO
		 * Para cada key, obtengo el value y para cada elemento del value (ya que es un array) escribo con la API RSS Writer
		 */
		
		$feedWriter = new FeedWriter ( RSS2 );
		
		// Aca obtengo un array con los nombres de los elementos
		$nombresDeElementos = array_keys ( $this->elementsMap );
		
		foreach ( $nombresDeElementos as $nombreElemento ) {
			
			// Aca obtengo el vector asociado a la key (el value) que es lo que debo escribir con la API
			$arrayDeContenidoDeElementos = array_shift ( $this->elementsMap );
			// Aca obtengo el nombre del primer elemento en el array de nombres de elementos
			

			foreach ( $arrayDeContenidoDeElementos as $contenido ) {
				
				// TODO: Si el elemento es un item, debo hacer lo siguiente:
				//				if($nombreElemento == "item"){
				//					
				//					$newItem = $feedWriter->createNewItem();
				//					
				//					
				//					$feedWriter->addItem($newItem);
				//					
				//					}					
				

				$elementoCapitalized = ucfirst ( $nombreElemento );
				
				$method = "set" . "$elementoCapitalized";
				
				$reflectionMethod = new ReflectionMethod ( 'FeedWriter', "$method" );
				
				$reflectionMethod->invoke ( $feedWriter, "$contenido" );
			
			}
		
		}
		
		return $feedWriter->generateFeed ();
	
	}

}

?>