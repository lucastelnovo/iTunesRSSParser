<?php

class Parser {
	
	private $urlsRss;
	private $channelElementsTemplate;
	private $itemElementsTemplate;
	private $validFeed;
	
	public function __construct($urlsRss) {
		
		$this->urlsRss = $urlsRss;
		$this->channelElementsTemplate = simplexml_load_file ( "../resources/HeaderTemplate.xml" )->channel;
		$this->itemElementsTemplate = simplexml_load_file ( "../resources/ItemTemplate.xml" )->item;
		$this->validFeed = new SimpleXMLElement ( "<rss xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" xmlns:itunesu=\"http://www.itunesu.com/feed\" version=\"2.0\"></rss>" );
	
	}
	
	public function haceTuMagia() {
		
		$domFeed = dom_import_simplexml ( $this->validFeed );
		
		// Agrega al rssSimpleXml las keys con el contenido default del
		// template que no est�n en �l. y hace m�s magia tambi�n.
		$domHeader = $this->channelElementsTemplate;
		$dom = dom_import_simplexml ( $domHeader );
			/*
			 * Esto sirve para appendear al valid feed el nodo que acabo de
			 * convertir
			 */
			
		$domHeader = $domFeed->ownerDocument->importNode ( $dom, TRUE );
		$domFeed->appendChild ( $domHeader );

		foreach ( $this->urlsRss as $url ) {
			
			$text = file_get_contents ( $url );
			
			//Cambio tags de iTunes para poder manejarlos sin ns.
			$url_modified = $this->changeDocTags ( $text, "<itunes:", "<itunes-" );
			$url_modified = $this->changeDocTags ( $url_modified, "</itunes:", "</itunes-" );
			
			//Cambio los & por AMPERSAND para que no rompa.
			$url_modified = $this->changeDocTags ( $url_modified, "&", "AMPERSAND" );
			$url_modified = $this->changeDocTags ( $url_modified, "&amp;", "AMPERSAND" );
			
			$rssSimpleXmlChannel = new SimpleXMLElement ( $url_modified );
			
			$rssSimpleXmlChannel = $rssSimpleXmlChannel->channel;
			
			
			
			
			// Agrego los items.
			foreach ( $rssSimpleXmlChannel->item as $item => $itemNode ) {
				$xmlItem = $this->compareElements ( $itemNode, $this->itemElementsTemplate );
				
				$domI = dom_import_simplexml ( $xmlItem );
				$domItem = $domHeader->ownerDocument->importNode ( $domI, TRUE );
				// Al domheader debo pedirle el channel para que lo appendee bien.
				$domHeader->appendChild ( $domItem );
			
			}
			
		
		}
		
		$unicornio = $this->validFeed->asXML ();
		
		$unicornio = $this->changeDocTags ( $unicornio, "<itunes-", "<itunes:" );
		$unicornio = $this->changeDocTags ( $unicornio, "</itunes-", "</itunes:" );
		$unicornio = $this->changeDocTags ( $unicornio, "<guid", "<guid isPermaLink=\"false\"");
		$unicornio = $this->changeDocTags ( $unicornio, "AMPERSAND", "&#38;"  );
		
		return $unicornio;
	}
	
	private function compareElements(SimpleXMLElement $elementToCompare, SimpleXMLElement $template) {
		
		$rootName = $elementToCompare->getName ();
		$rssAux = new SimpleXMLElement ( "<" . $rootName . "></" . $rootName . ">" );
		$children = $template->children ();
		
		foreach ( $children as $child_name => $child_node ) {
			
			$elementPresentInChannel = $elementToCompare->xpath ( "$child_name" );
			
			if ($child_name == "itunes-image") { // Porque este tag en "unario"
				$this->handleImageElement ( $child_node, $elementPresentInChannel, $rssAux );
				continue;
			} elseif ($child_name == "enclosure") {
				$this->handleEnclosureElement ( $child_node, $elementPresentInChannel, $rssAux );
				continue;
			}
			
			$cant = count ( $child_node ); // En esta variable cuento la cantidad
			                               // de elementos del nodo (para ver si
			                               // tiene
			                               // hijos)
			
			$element = $this->addChildren ( $rssAux, $elementPresentInChannel, $child_node );
			
			if ($cant > 0) { // Si el nodo tiene hijos..
				foreach ( $child_node as $subhijos_name => $subhijos_node ) {
					$elementPresentInSon = $elementToCompare->$child_name->xpath ( "$subhijos_name" );
					$this->addChildren ( $rssAux->$child_name, $elementPresentInSon, $subhijos_node );
				}
			}
		
		}
		
		return $rssAux;
	
	}
	
	private function addChildren($elementToAddChild, $elementPresentInChannel, $child_node) {
		
		$child_name = $child_node->getName ();
		
		// Si no existe el elemento en el RSS que me llega...
		if (! $elementPresentInChannel) {
			
			// Agrego el contenido del template
			$element = $elementToAddChild->addChild ( $child_name, ( string ) $child_node [0] );
		
		} else {
			
			// Si no, agrego el mismo contenido que tenia el elemento.
			//$element->child_name = $elementPresentInChannel [0];
			$element = $elementToAddChild->addChild ( $child_name, ( string ) $elementPresentInChannel [0] );
		}
		
		return $element;
	}
	
	private function changeDocTags($text, $search, $replace) {
		
		$changedText = str_replace ( $search, $replace, $text );
		
		return $changedText;
	
	}
	
	private function handleImageElement(SimpleXMLElement $child_node, $elementPresentInChannel, SimpleXMLElement $elementToAddChild) {
		
		$child_name = $child_node->getName ();
		
		if (! $elementPresentInChannel) {
			
			$child = $elementToAddChild->addChild ( $child_name );
			$child->addAttribute ( "href", "http://images.apple.com/pr/images/rotation/leopardbox.jpg" );
		
		} else {
			
			$child = $elementToAddChild->addChild ( $child_name );
			$child->addAttribute ( "href", $elementPresentInChannel [0]->attributes () );
		
		}
	
	}
	
	private function handleEnclosureElement(SimpleXMLElement $child_node, $elementPresentInChannel, SimpleXMLElement $elementToAddChild) {
		
		$child_name = $child_node->getName ();
		
		if (! $elementPresentInChannel) {
			
			$child = $elementToAddChild->addChild ( $child_name );
			$child->addAttribute ( "url", "http://example.com/podcasts/RSS-Basics.m4a" );
			$child->addAttribute ( "length", "1" );
			$child->addAttribute ( "type", "audio/x-m4a" );
		
		} else {
			
			$child = $elementToAddChild->addChild ( $child_name );
			$atributos = $elementPresentInChannel [0]->attributes ();
			
			$child->addAttribute ( "url", $atributos ['url'] );
			$child->addAttribute ( "type", $atributos ['type'] );
			
			if($atributos['lenght'] == "")
				$child->addAttribute ( "length", "1" );
			else			
			$child->addAttribute ( "length", $atributos ['lenght'] );
		
		}
	
	}

}
?>