<?php
/* Script Base by David Zimmerman http://www.dizzysoft.com/

Rebuild by M. Vattersen http://www.mvattersen.de/

Use a cronjob to push logdata from system to file and parse the data to an RSS Feed

Please feel free to use as long as you give me credit and understand there is no warranty that comes with this script.
*/

// define some constants
define('APPNAME', str_replace('/', '', dirname($_SERVER['PHP_SELF'])) );	// Pure foldername
define('APPLANG', substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) );			// browser lang (en, de, etc)	
define('PATH', $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));
define('URL', 'http://' . PATH);

// check for installer file to help install the neccessary cronjob
if (file_exists('install.php'))
{
	// Load the installation check
	return include 'install.php';
}


$file = "tmp.log";
$max = 10; // max number of entries

$lines= array_reverse(file($file));

$url= 'http://'.$_SERVER['HTTP_HOST'];
$url.= substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'],'/')+1);

$doc= new DomDocument('1.0', 'UTF-8');
$doc->formatOutput = true;
$doc->preserveWhiteSpace = false;
$doc->substituteEntities = TRUE;

// create root node
$root = $doc->createElement('rss');
$doc->appendChild($root);

$version = $doc->createAttribute('version');
$root->appendChild($version);
$text= $doc->createTextNode('2.0');
$version->appendChild($text);

$channel= $doc->createElement('channel');
$root->appendChild($channel);

// nodes of channel
$info= $doc->createElement('title');
$channel->appendChild($info);
$text= $doc->createTextNode(APPNAME . ' RSS Feed');
$info->appendChild($text);

$info= $doc->createElement('description');
$channel->appendChild($info);
$text= $doc->createTextNode("The RSS Newsfeed from $url");
$info->appendChild($text);

$info= $doc->createElement('link');
$channel->appendChild($info);
$text= $doc->createTextNode($url);
$info->appendChild($text);

$info= $doc->createElement('lastBuildDate');
$channel->appendChild($info);
$text= $doc->createTextNode(Date('r')); // now
$info->appendChild($text);

// If we got less than max set max to highest counted 
$counter = count($lines);
if ($counter < $max) {
  $max = $counter;
}

// items for this channel
//foreach($lines as $line) {
for ($i=0;$i<$max;$i++) { 

$line = $lines[$i];

$item = $doc->createElement('item');
$channel->appendChild($item);

$child = $doc->createElement('title');
$item->appendChild($child);
//	Regex will convert this:
//		  This [text [more text]][also text] is cool
//	to this:
//		  This is cool
$title = substr(substr(preg_replace("/\[([^\[\]]++|(?R))*+\]/", "", $line), 0, -1), 3);
$value = $doc->createTextNode($title);
$child->appendChild($value);

$child = $doc->createElement('link');
$item->appendChild($child);
$value = $doc->createTextNode( $url );
$child->appendChild($value);

$child = $doc->createElement('description');
$item->appendChild($child);
$value = $doc->createTextNode( substr($line, 0 , -1) );
$child->appendChild($value);

$child = $doc->createElement('pubDate');
$item->appendChild($child);
$date= substr($line, 1, 19);
$value = $doc->createTextNode(date('r', strtotime($date)));
$child->appendChild($value);

}
echo $doc->saveXML();
?>
