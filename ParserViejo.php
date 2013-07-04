<?php

include_once 'API FeedWriter/FeedWriter.php';

/*
 * Referencia y notas:
 * TODO, cosas que falta implementar.
 * TODO!, cosa importante a revisar.
 * TODO ASK, cosa a preguntar
 * TODO CHECK, puede ser inconsistente.
 * 
 * Los jdocs por lo pronto los estamos poniendo inmediatamente debajo de los métodos, para facilitar !expandirlos. Maybe los tengamos que pasar arriba luego. No problem. No hay demasiado código.
 * Los comments con // pueden ser borrados luego. Son de guía para poder codear y seguir el código tranqui. No son indispensables, pero backupear de ser posible para potencial manutención de código.
 * 
 * Falta poner los jdocs de parámetros, return, author.
 * 
 * Asume que $defaultElementsMap tiene elementos con contenidos y sin hijos. En todo caso, agregar code al writer.
 */

class ParserViejo {
	
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
		$validFeed .= "<rss xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:dcterms=\"http://purl.org/dc/terms/\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" version=\"2.0\">" . "\n";
		
		//foreach archivo pasado a parsear
		foreach ( $this->urlsRss as $url ) {
			
			//extrae la info al mapa y luego la inserta en el nuevo feed, con raiz channel; llena con la info default la info que no esté
			$this->extractAndWriteElements($validFeed, $url, $this->defaultElementsMap);
						
			//limpia el mapa
			erase_values ( $this->elementsMap );
		}	
		
		//returnea el nuevo feed
		return $validFeed;
	}
	
	private function erase_values(&$myarr) {
		/*TODO CHECK. No creo que funque, no es un map. Hay garbage collector?
		 * Borra las values de un mapa, pero conserva las keys. Función genérica.
		 */
		
		$myarr = array_map ( create_function ( '$n', 'return null;' ), $myarr );
	}
	
	public function extractAndWriteElements($feed, $url, $defaultElementsMap){
		/* TODO! Quizá el resto de los métodos son privados..
		 * Wrapper de la función extractAndWriteElementsInt. Crea el cursor, y la root inicial. Método por comodidad (el otro quedaría muy grande sino).
		 */
				
		return $this->extractAndWriteElements("channel", $feed, $url, $defaultElementsMap, $cursorRoot = array());
	}
	
	public function extractAndWriteElementsInt($root, $feed, $url, $defaultElementsMap, $cursorRoot) {
		/* TODO CHECK sintaxis ("$root" y los this.. todo, per las dudetas.) Revisar también si cuando se pasa una variable, se pasa la dirección del array, o una copia!!
		 * Extrae el contenido de cada elemento de la lista y de los hijos de esos elementos, e inserta sus contenidos en el nuevo feed. Devuelve el feed.
		 */ 
		
		//hago que php lea la url como un xml
		$xmlUrl = new simplexml_load_file($url);
		
		//le agrega la posición actual al cursor
		array_push($cursorRoot, $root);
		
		//abre la raiz con nombre del array, que es el elemento.
		$feed .= "<$root>" . "\\n";
		
		//foreach elemento de la lista
		foreach ( $this->elementsMap as $element ) {
		
			//agrega el contenido del elemento al array de contenidos del elemento, y los hijos como strings al array de hijos, y hace lo mismo para sus hijos.
			$this->addElementContents($element, $cursorRoot, $xmlUrl);
			
			//addElementSons devuelve true si agregó algún hijo al array, false si no había nada qué agregar.
			if(addElementSons($this->elementsMap, $element, $url, $cursorRoot)){
				$this->extractAndWriteElements($element, $feed, $xmlUrl, $cursorRoot);
			}	
		}
		
		//después de recorrer la lista de elementos, los escribo en el feed
		$feed .= $writeElements($root);
		
		//cierra la raiz
		$feed .= "\\n" . "</$root>";
		
		//el cursor "vuelve" al elemento anterior en el árbol
		array_pop($cursorRoot);
		
		return $feed;
	}
	
	public function addElementContents($element, $cursorRoot, $xmlRss/*$elementsMap, $element, $url, $cursorRoot*/){
		/* TODO CHECK
		 * Agrega el contenido del elemento al array de contenidos del elemento, y los hijos como strings al array de hijos, y hace lo mismo para sus hijos.
		 */	
				
		$actualRoot = array();
		
		foreach ($cursorRoot as $father){
			$actualRoot .= "->";
			$actualRoot .= "$father";//TODO! CHECK está bien la sintaxis con comillas?
		}
		//para cada elemento del mapa de elementos
		
		//le pide al xmlRss los elementos con el nombre del elemento pasado por parámetro, con el cursor como padre
		$elements = $xmlRss->$cursorRoot; //TODO! CHECK cómo joraca escupo esto en forma de string?
		
		//y agrega los contenidos al array de contenidos del elemento, usando el cursor nuevamente
		
		
			

	/*
			$rss = simplexml_load_file($url);
			if($rss){
				echo '<h1>'.$rss->channel->title.'</h1>';
				echo '<li>'.$rss->channel->pubDate.'</li>';
				$items = $rss->channel->item;
				
				foreach($items as $item){
					$title = $item->title;
					$link = $item->link;
					$published_on = $item->pubDate;
					$description = $item->description;
					echo '<h3><a href="'.$link.'">'.$title.'</a></h3>';
					echo '<span>('.$published_on.')</span>';
					echo '<p>'.$description.'</p>';
				}
			}
	*/
	}
	
	public function addElementSons($elementsMap, $element, $url, $cursorRoot){
		/* TODO! Quedé acá. Qué hace esto, specifically?
		 * Agrega a los hijos de un elemento al array de hijos. Devuelve true si agregó algún hijo al array, false si no había nada qué agregar.
		 */
		
		//abre el rss desde la url
		
		//para cada hijo elemento del mapa de elementos
		
		//le pide al rss los elementos con el nombre del elemento pasado por parámetro, con el cursor como padre
		
		//
	
		return ; //tamaño del array
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