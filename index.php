<?php
/* Script Base by David Zimmerman http://www.dizzysoft.com/

Rebuild by Maik Vattersen http://www.exigem.com/

Use a cronjob to push logdata from system to file

Please feel free to use as long as you give me credit and understand there is no warranty that comes with this script.
*/

// check for installer file
if (file_exists('install.php'))
{
	define('PATH', $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));
	define('URL', 'http://' . PATH);
	define('APPNAME', str_replace('/', '', dirname($_SERVER['PHP_SELF'])) );
	define('APPLANG', substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) );
	
	// Load the installation check
	return include 'install.php';
}


$file = "tmp.log";
$max = 3; // max number of entries

$lines= array_reverse(file($file));
//$lines = array_unique($lines);
  
//echo "<pre>";
//print_r($lines);
//echo "</pre>";

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

$content = $doc->createAttribute('xmlns:content'); $root->appendChild($content); 
$text= $doc->createTextNode('http://purl.org/rss/1.0/modules/content/'); $content->appendChild($text);

$wfw = $doc->createAttribute('xmlns:wfw'); $root->appendChild($wfw);
$text= $doc->createTextNode('http://wellformedweb.org/CommentAPI/'); $wfw->appendChild($text);

$dc = $doc->createAttribute('xmlns:dc'); $root->appendChild($dc);
$text= $doc->createTextNode('http://purl.org/dc/elements/1.1/'); $dc->appendChild($text);

$atom = $doc->createAttribute('xmlns:atom'); $root->appendChild($atom);
$text= $doc->createTextNode('http://www.w3.org/2005/Atom'); $atom->appendChild($text);

$sy = $doc->createAttribute('xmlns:sy'); $root->appendChild($sy);
$text= $doc->createTextNode('http://purl.org/rss/1.0/modules/syndication/'); $sy->appendChild($text);

$slash = $doc->createAttribute('xmlns:slash'); $root->appendChild($slash);
$text= $doc->createTextNode('http://purl.org/rss/1.0/modules/slash/'); $slash->appendChild($text);

$channel= $doc->createElement('channel');
$root->appendChild($channel);

// nodes of channel
$info= $doc->createElement('title');
$channel->appendChild($info);
$text= $doc->createTextNode('Error log for '.$url);
$info->appendChild($text);
$info= $doc->createElement('link');
$channel->appendChild($info);
$text= $doc->createTextNode($url.'error_log');
$info->appendChild($text);
$info= $doc->createElement('description');
$channel->appendChild($info);
$text= $doc->createTextNode("This is the Ban log for $url");
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
$title = preg_replace("/\[([^\[\]]++|(?R))*+\]/", "", $line);
//	Regex will convert this:
//		  This [text [more text]][also text] is cool
//	to this:
//		  This is cool
$title = /* strpos($line, "\n", 50) . " " . */ $title ;
$value = $doc->createTextNode($title);
$child->appendChild($value);

$child = $doc->createElement('description');
$item->appendChild($child);
$value = $doc->createTextNode($line);
$child->appendChild($value);

$child = $doc->createElement('pubDate');
$item->appendChild($child);
$date= substr($line, 1, 19);
$value = $doc->createTextNode(date('r', strtotime($date)));
$child->appendChild($value);

}
echo $doc->saveXML();
?>
