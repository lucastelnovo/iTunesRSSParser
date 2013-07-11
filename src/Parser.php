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
		$this->validFeed = new SimpleXMLElement ( "<rss></rss>" );
	
	}
	
	public function haceTuMagia() {
		
		$domFeed = dom_import_simplexml ( $this->validFeed );
		
		foreach ( $this->urlsRss as $url ) {
			
			$text = file_get_contents ( $url );
			
			$url_modified = $this->changeDocTags ( $text, "<itunes:", "<itunes-" );
			$url_modified = $this->changeDocTags ( $url_modified, "</itunes:", "</itunes-" );
			
			$rssSimpleXmlChannel = new SimpleXMLElement ( $url_modified );
			
			$rssSimpleXmlChannel = $rssSimpleXmlChannel->channel;
			
			// Agrega al rssSimpleXml las keys con el contenido default del
			// template que no est�n en �l. y hace m�s magia tambi�n.
			$domHeader = $this->compareElements ( $rssSimpleXmlChannel, $this->channelElementsTemplate );
			
			$dom = dom_import_simplexml ( $domHeader );
			/*
			 * Esto sirve para appendear al valid feed el nodo que acabo de
			 * convertir
			 */
			
			$domHeader = $domFeed->ownerDocument->importNode ( $dom, TRUE );
			
			// Agrego los items.
			foreach ( $rssSimpleXmlChannel->item as $item => $itemNode ) {
				$xmlItem = $this->compareElements ( $itemNode, $this->itemElementsTemplate );
				
				$domI = dom_import_simplexml ( $xmlItem );
				$domItem = $domHeader->ownerDocument->importNode ( $domI, TRUE );
				$domHeader->appendChild ( $domItem );
			
			}
			
			$domFeed->appendChild ( $domHeader );
		
		}
		
		return $this->validFeed->asXML ();
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
			} elseif ($child_name == "itunes-enclosure") {
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
		// TODO
		$child_name = $child_node->getName ();
		
		if (! $elementPresentInChannel) {
			
			$child = $elementToAddChild->addChild ( $child_name );
			$child->addAttribute ( "href", "http://images.apple.com/pr/images/rotation/leopardbox.jpg" );
		
		} else {
			
			$child = $elementToAddChild->addChild ( $child_name );
			$child->addAttribute ( "href", $elementPresentInChannel [0]->attributes () );
		
		}
	
	}

}
?>