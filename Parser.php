<?php

class Parser {
	
	private $urlsRss; // Lista con los rss a convertir a formato iTunes.
	private $channelElementsTemplate; // Mapa que tiene como key las rutas de los elementos que son v�lidos para iTunes, y como values, los valores default.	
	private $itemElementsTemplate; //capo
	private $validFeed;
	
	public function __construct($urlsRss) {
		
		$this->urlsRss = array ($urlsRss );
		$this->channelElementsTemplate = simplexml_load_file ( "HeaderTemplate.xml" )->channel;
		$this->itemElementsTemplate = simplexml_load_file ( "ItemTemplate.xml" )->item;
		$this->validFeed = new SimpleXMLElement ( "<rss></rss>" );
	
	}
	
	public function haceTuMagia() {
		
		$domFeed = dom_import_simplexml ( $this->validFeed );
		
		foreach ( $this->urlsRss as $url ) {
			
			$rssSimpleXmlChannel = simplexml_load_file ( $url )->channel;
			
			//Agrega al rssSimpleXml las keys con el contenido default del template que no est�n en �l. y hace m�s magia tambi�n.
			$domAux = $this->compareElements ( $rssSimpleXmlChannel, $this->channelElementsTemplate );
			$iTunesTags = $this->agregarTagsiTunes ( $rssSimpleXmlChannel );
			
			foreach ( $iTunesTags->children () as $iTunesElementName => $iTunesElementNode ) {
				
				$domAux->addChild ( $iTunesElementName, ( string ) $iTunesElementNode [0] );
				$cant = count ( $iTunesElementNode );
				
				if ($cant > 0) { // Si el nodo tiene hijos..			
					foreach ( $iTunesElementNode as $subhijos_name => $subhijos_node ) {
						$domAux->iTunesElementNode->addChild ( $subhijos_name, ( string ) $subhijos_node [0] );
					}
				}
			
			}
			
			$dom = dom_import_simplexml ( $domAux );
			$domAux = $domFeed->ownerDocument->importNode ( $dom, TRUE ); // Esto sirve para appendear al valid feed el feed que acabo de convertir			
			$domFeed->appendChild ( $domAux );
		
		//			foreach($rssSimpleXmlChannel->item as $item){
		//				$domItemAux = $this->compareElements();
		//			}
		}
		
		return $this->validFeed->asXML ();
	}
	
	private function compareElements(SimpleXMLElement $elementToCompare, SimpleXMLElement $template) {
		
		$rootName = $elementToCompare->getName ();
		$rssAux = new SimpleXMLElement ( "<" . $rootName . "></" . $rootName . ">" );
		$children = $template->children ();
		$ns = "";
		
		if ($rootName == "itunes") {
			$children = $template;
			$ns = "itunes";
		}
		
		foreach ( $children as $child_name => $child_node ) {
			
			$elementPresentInChannel = $elementToCompare->xpath ( "$child_name" );
			
			$cant = count ( $child_node ); // En esta variable cuento la cantidad de elementos del nodo (para ver si tiene hijos)
			

			$element = $this->addChildren ( $rssAux, $elementPresentInChannel, $child_node, $ns );
			
			if ($cant > 0) { // Si el nodo tiene hijos..			
				foreach ( $child_node as $subhijos_name => $subhijos_node ) {
					$element->addChild ( $subhijos_name, ( string ) $subhijos_node [0] );
				}
			}
		
		}
		
		return $rssAux;
	}
	
	private function addChildren($rssAux, $elementPresentInChannel, $child_node, $namespace) {
		
		$child_name = $child_node->getName ();
		
		if (! $elementPresentInChannel) { // Si no existe el elemento en el RSS que me llega...
			

			$element = $rssAux->addChild ( $namespace . $child_name, ( string ) $child_node [0] ); // Agrego el contenido del template.
		

		} else {
			
			$element = $rssAux->addChild ( $namespace . $child_name, ( string ) $elementPresentInChannel [0] ); // Si no, agrego el mismo contenido que tenia ese elemento.
		

		}
		
		return $element;
	}
	
	public function agregarTagsiTunes(SimpleXMLElement $channel) {
		
		$iTunesNamespaceElements = $this->channelElementsTemplate->children ( "http://www.itunes.com/dtds/podcast-1.0.dtd" );
		
		$iTunesAux = new SimpleXMLElement ( "<itunes></itunes>" );
		
		$iTunesAux->addChild ( $channel->getName () );
		
		return $this->compareElements ( $iTunesAux, $iTunesNamespaceElements );
	
	}
	
/*
		xmlns:atom=\"http://www.w3.org/2005/Atom\"
		xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"
		xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
		xmlns:dcterms=\"http://purl.org/dc/terms/\"
		xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
		xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" 
		version=\"2.0\">"
		*/
}
?>