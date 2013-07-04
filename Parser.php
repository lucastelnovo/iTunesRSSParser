<?php

class Parser {
	private $urlsRss; // Lista con los rss a convertir a formato iTunes.
	private $channelElementsTemplate; // Mapa que tiene como key las rutas de los elementos que son válidos para iTunes, y como values, los valores default.	
	
	public function __construct($urlsRss, $urlTemplate) {
		$this->urlsRss = $urlsRss;
		$this->channelElementsTemplate = simplexml_load_file($urlTemplate);
		
		return;
	}
	
	public function haceTuMagia(){
		$validFeed = new SimpleXMLElement();
		
		$validFeed->addChild(rss);
		$validFeed->rss->addAttribute("xmlns:atom","asd");
		/*TODO blah*/		
		
		foreach ( $this->urlsRss as $url ){
			$rssSimpleXmlChannel = simplexml_load_file($url)->channel;
			
			//Agrega al rssSimpleXml las keys con el contenido default del template que no estén en él. y hace más magia también.
			$validFeed->addChild($this->compareElements($rssSimpleXmlChannel));
		}
		
		return $validFeed->asXML();
	}
	
	
	private function compareElements($channel){
		$rssAux = new SimpleXMLElement();
		
		foreach($this->channelElementsTemplate->children() as $element){
			$channel->$element ? $rssAux->addChild($channel->$element) : $rssAux->addChild($element);
		}
		
		foreach($channel->children() as $element){
			if(!$this->channelElementsTemplate->$element){
				$rssAux->addChild($channel->$element);
			}
		}
			
		return $rssAux;
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