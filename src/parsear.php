<?php

include 'Auxiliar.php';
include 'Parser.php';

 //$urls = array($_REQUEST['url1'], $_REQUEST['url2'], $_REQUEST['url3']);

// HARDCODED
$urls = array ("http://feeds.feedburner.com/rhbmixcloud", "http://feeds.feedburner.com/rhbyoutube", "http://feeds.feedburner.com/remembergroovesivoox" );

$miParser = new Parser ( $urls );

$validFeed = $miParser->haceTuMagia ();

$aux = new Auxiliar ( "../iTunesValidFeed" );
$aux->devolverXML ( $validFeed );

header("Location: ../iTunesValidFeed");

?>
