<?php

class Parser {
	private $urlsRss; // Lista con los rss a convertir a formato iTunes.
	private $channelElementsTemplate; // Mapa que tiene como key las rutas de los elementos que son válidos para iTunes, y como values, los valores default.	
	
	public function __construct($urlsRss, $urlTemplate) {
		$this->urlsRss = array($urlsRss);
		$this->channelElementsTemplate = simplexml_load_file($urlTemplate)->channel;
		
		return;
	}
	
	public function haceTuMagia(){
		 
		$validFeed = new SimpleXMLElement("<rss></rss>");
		$domFeed = dom_import_simplexml($validFeed);
		
		//$validFeed->addChild(rss);
		//$validFeed->rss->addAttribute("xmlns:atom","asd");
		/*TODO blah*/		
		
		foreach ( $this->urlsRss as $url ){
			
			$rssSimpleXmlChannel = simplexml_load_file($url)->channel;
			
			//Agrega al rssSimpleXml las keys con el contenido default del template que no estén en él. y hace más magia también.
			$domAux = $this->compareElements($rssSimpleXmlChannel);
			$domAux = $domFeed->ownerDocument->importNode($domAux, TRUE);
			$domFeed->appendChild($domAux);
		}
		
		
		return $validFeed->asXML();
	}
	
	
	private function compareElements(SimpleXMLElement $channel){
		$rssAux = new SimpleXMLElement("<channel></channel>");

		foreach($this->channelElementsTemplate->children() as $child_name=>$child_node){
			
			$elementPresentInChannel = $channel->xpath("$child_name");
			
			if(!$elementPresentInChannel) // Si no existe el elemento en el RSS que me llega...
			$rssAux->addChild($child_name, (string) $child_node[0]); // Agrego el contenido del template.
			else
			$rssAux->addChild($child_name, (string) $elementPresentInChannel[0]); // Si no, agrego el mismo contenido que tenia ese elemento.
		}
		
		/*foreach($channel->children() as $element2){
			if(!$this->channelElementsTemplate->$element2){
				$rssAux->addChild($channel->$element2);
			}
		}*/
			
		return dom_import_simplexml($rssAux);
	}


		
		/*
		xmlns:atom=\"http://www.w3.org/2005/Atom\"
		xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"
		xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
		xmlns:dcterms=\"http://purl.org/dc/terms/\"
		xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
		xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" 
		version=\"2.0\">"*/
}
?>