<?php
require('dbinfo.php');

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
    $query = "SELECT * FROM googlemapdemo WHERE 1";
    $result = $db->query($query);
    if (!$result) {
        die("Invalid query!");
    }
    $markers = $result->fetchAll(PDO::FETCH_ASSOC);

    header("Content-type: text/xml");

    foreach ($markers as $marker) {
        // Add to XML document node
        $node = $dom->createElement("marker"); //Create a elemnt named "marker".
        $newnode = $parnode->appendChild($node); //Append "marker" as child to "markers".

        //Set properties in "marker".
        $newnode->setAttribute("id", $marker['id']);
        $newnode->setAttribute("name", $marker['name']);
        $newnode->setAttribute("address", $marker['address']);
        $newnode->setAttribute("lat", $marker['lat']);
        $newnode->setAttribute("lng", $marker['lng']);
        $newnode->setAttribute("type", $marker['type']);
    }
    echo $dom->saveXML();
    $db=null;
} catch (PDOException $e) {
    var_dump($e->getMessage());
}
