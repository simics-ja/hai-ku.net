<?php
require("dbinfo.php");
require("path_utility.php");

$limit = 6;
if(isset($_GET['page'])){
  $page=$_GET['page'];
}

if(isset($_GET['now'])){
  $time = $_GET['now'];
}

$offset = ($page-1)*$limit + $limit;

try {
  //connect
  $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $db->query("SELECT * FROM " . DB_TABLE . " WHERE created <= CAST('" . $time . "' AS DATETIME) ORDER BY created DESC LIMIT " . $limit . " OFFSET " . $offset);
  $num = $stmt->rowCount();
  $new_haikus = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  $message = $e->getMessage();
}

if($num < 1){
  echo '<row class="col-lg-12"><p style="text-align:center;">これより古い投稿はありません。</p></row>';
}else{
  $html ="";
  foreach ($new_haikus as $haiku) {
      $dom = new DOMDocument();
      mb_convert_variables("UTF-8", "EUC-JP", $haiku);
      $dom->loadHTML(mb_convert_encoding(file_get_contents("./component/haikuview.html"), 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS'));
      $dom->getElementById("hv-image")->setAttribute("src", PathUtility::getPathById($haiku['uuid'], $haiku['photoid'], LARGE_THUMB));
      $ele = $dom->createElement("p");
      $ele->appendChild($dom->createTextNode(htmlspecialchars($haiku['haiku'], ENT_QUOTES, 'UTF-8')));
      $dom->getElementById("hv-haiku")->appendChild($ele);
      $ele = $dom->createElement("p");
      $ele->appendChild($dom->createTextNode(htmlspecialchars($haiku["name"], ENT_QUOTES, 'UTF-8') . "さんの投稿"));
      $ele->setAttribute("style", "text-align:right");
      $ele->setAttribute("id", "hv-poster");
      $dom->getElementById("hv-body")->appendChild($ele);
      $dom->removeChild($dom->doctype);
      $dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);
      $html .= mb_convert_encoding($dom->saveHTML(), 'utf-8', 'HTML-ENTITIES');
  }
  echo $html;
  
}
?>
