<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>iTunes CSS Parser</title>
</head>

<body>
<?php

include 'Parser.php';

/*
 * VA HARDCODEADO PARA TESTEAR
 */

// $urls = array($_REQUEST['url1'], $_REQUEST['url2'], $_REQUEST['url3']);

$urls = array("http://feeds.feedburner.com/rhbmixcloud");

$miParser = new Parser ( $urls );

$validFeed = $miParser->haceTuMagia ();

echo "$validFeed";

?>
</body>
</html>
