<?php
require('dbinfo.php');
require('path_utility.php');
ini_set("display_errors", 0);
// Start XML file, create parent node
$dom = new DOMDocument("1.0", "utf-8"); //Create XML Documet.
$node = $dom->createElement("markers");  //Create a element named "markers".
$parnode = $dom->appendChild($node); //Append "markers" as child to XML.

try {
    // Opens a connection to a MySQL server
    $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (!$db) {
        die("DB connection failed!");
    }
    // Select all the rows in the googlemapdemo table
    $query = 'SELECT * FROM ' . DB_TABLE . ' WHERE lat IS NOT NULL';
    $result = $db->query($query);
    if (!$result) {
        die("Invalid query!");
    }
    $markers = $result->fetchAll(PDO::FETCH_ASSOC);

    header("Content-type: text/xml");

    foreach ($markers as $marker) {
        // Add to XML document node
        mb_convert_variables("UTF-8", "EUC-JP", $marker);
        $node = $dom->createElement("marker"); //Create a elemnt named "marker".
        $newnode = $parnode->appendChild($node); //Append "marker" as child to "markers".

        //Set properties in "marker".
        $newnode->setAttribute("name", $marker['name']);
        $newnode->setAttribute("haiku", $marker['haiku']);
        $newnode->setAttribute("imgpath", PathUtility::getPathById($marker['uuid'], $marker['photoid'], SMALL_THUMB));
        $newnode->setAttribute("lat", $marker['lat']);
        $newnode->setAttribute("lng", $marker['lng']);
        $newnode->setAttribute("created", $marker['created']);
    }
    echo $dom->saveXML();
    $db=null;
} catch (PDOException $e) {
    var_dump($e->getMessage());
}

?>
