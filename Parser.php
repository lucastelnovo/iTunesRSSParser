<?php

include_once 'API FeedWriter/FeedWriter.php';

/*
 * Referencia y notas:
 * TODO, cosas que falta implementar.
 * TODO!, cosa importante a revisar.
 * TODO ASK, cosa a preguntar
 * TODO REVISAR, puede ser inconsistente.
 * 
 * Los jdocs por lo pronto los estamos poniendo inmediatamente debajo de los métodos, para facilitar !expandirlos. Maybe los tengamos que pasar arriba luego. No problem. No hay demasiado código.
 * Los comments con // pueden ser borrados luego. Son de guía para poder codear y seguir el código tranqui. No son indispensables, pero backupear de ser posible para potencial manutención de código.
 * 
 * Falta poner los jdocs de parámetros, return, author.
 */

class Parser {
	
	private $urlsRss; // Lista con los rss a convertir a formato iTunes
	private $elementsList; // Lista en forma de strings con elemementos obligatorios
	private $elementsMap; // Mapa con (elemento, array con la info del elemento (contenidos, hijos))
	private $defaultInformationMap; // Mapa con (elemento, valor_default)

	public function __construct($urlsRss, $elementsList, $defaultInformationMap) {
		/*
		 * Constructor. Inicializa urlsRss, defaultInformationMap, elementsList, y crea el elementsMap. 
		 */
		
		$this->urlsRss = $urlsRss;
		$this->defaultInformationMap = $defaultInformationMap;
		$this->elementsList = $elementsList;
		$this->elementsMap = $this->createElementsMap ();
	}
	
	public function createElementsMap() {
		/* TODO Ver cómo pasar el array base, y crear bien los nombres de las keys y los hijos.
		 * Crea el mapa de elementos, con key = element, elementInfo = array de dos arrays: uno con el contenido del elemento, y otro con los hijos, ambos inicializados vacíos.
		 */
		
		// Defino un array en el que guardo los elementos que seran las keys del mapa
		$elements = array ();
		
		// Para cada elemento de la lista de elementos, le asigno el nombre del elemento como key, y de value le asigna un array de dos keys (de nombres fijos "contents" y "sons", donde cada una tiene un array vacío.
		foreach ( $this->elementsList as $element ) {
			$elements[$element] = $elementsInfo = array($content = array(), $sons = array());
		};
		
		return $elements;
	}
	
	public function createValidFeed() {
		/* TODO! Maybe este sea el único método público..
		 * Devuelve un string con el nuevo RSS válido. Puede escalarse luego a que en lugar de devolver un string, lo echoee. 
		 */
		
		//escupe los headers al nuevo rss. TODO ASK Hay que chequear que no esté vacío?
		$newFeed .= "<rss xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:dcterms=\"http://purl.org/dc/terms/\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" version=\"2.0\">" . "\n";
		
		//foreach archivo pasado a parsear
		foreach ( $this->urlsRss as $url ) {
			
			//extrae la info al mapa y luego la inserta en el nuevo feed, con raiz channel
			$this->extractAndWriteElements("channel", $newFeed, $url);
						
			//limpia el mapa
			erase_values ( $this->elementsMap );
		}	
		
		//returnea el nuevo feed
		return $newFeed;
	}
	
	private function erase_values(&$myarr) {
		/*
		 * Borra las values de un mapa, pero conserva las keys. Función genérica.
		 */
		
		$myarr = array_map ( create_function ( '$n', 'return null;' ), $myarr );
	}
	
	public function extractAndWriteElements($root, $feed, $url) {
		/* TODO revisar sintaxis ("$root" y los this.. todo, per las dudetas.)
		 * Extrae el contenido de cada elemento de la lista y de los hijos de esos elementos, e inserta sus contenidos en el nuevo feed.
		 */ 
		
		//abre la raiz con nombre del array, que es el elemento.
		$feed .= "<$root>" . "\\n";
		
		//foreach elemento de la lista
		foreach ( $this->elementsMap as $element ) {
		
			//agrega el contenido del elemento al array de contenidos del elemento, y los hijos como strings al array de hijos, y hace lo mismo para sus hijos.
			addElementContents($this->elementsMap, $element, $url);
			
			//addElementSons devuelve true si agregó algún hijo al array, false si no había nada qué agregar.
			if(addElementSons($this->elementsMap, $element, $url)){
				extractElements($element);
			}
		}
		
		/*TODO! write acá*/
		
		//cierra la raiz
		$feed .= "\\n" . "</$root>";
		
		return;

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
	
	public function addElementContents($elementsMap, $element, $url){
		/* TODO
		 * Agrega el contenido del elemento al array de contenidos del elemento, y los hijos como strings al array de hijos, y hace lo mismo para sus hijos.
		 */	
	}
	
	public function addElementSons(){
		/* TODO
		 * Agrega a los hijos de un elemento a la chanceDevuelve true si agregó algún hijo al array, false si no había nada qué agregar.
		 */
		
	}
	
	public function writeElements() {
		/* TODO
		 * @INFO
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


		/*
		 * Para la función createValidFeed:
		 * Si no quiero usar la función privada de arriba (porque puede quedar fea una auxiliar no estática), uso este foreach de abajo.
		 *
		 * REF: http://php.net/manual/es/control-structures.foreach.php, http://stackoverflow.com/questions/2217160/delete-all-values-from-an-array-while-keeping-keys-intact, y esto es lindo http://stackoverflow.com/questions/9568044/php-remove-empty-null-array-key-values-while-keeping-key-values-otherwise-not-e
		 *
		 * foreach ( $this->elementsMap as $i => $value ) {
		 *	unset ( $array [$i] );
		 * }
		*/

		/* 
		 * Del método createElementsMap:
		 * 
		 * Apunta todo al mismo array?
		 * $emptyArray = array ();
		 * $keys [$string] = $emptyArray;
		*/	
		
?>