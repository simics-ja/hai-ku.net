<?php
require("dbinfo.php");
ini_set("display_errors", 0);
function judgeBingoClear()
{
    $rallypoints = array(
    'aramaki',
    'midorigaokakoen',
    'itamiryokuchi',
    'koyaikekoen',
    'eaonmall',
    'itamikuko',
    'kotobagura',
    'chojuzo',
    'gogadukakofun'
  );
    $clearpoints = array();
    $i = 0;
    foreach ($rallypoints as $point) {
        if (isset($_COOKIE[$point])) {
            array_push($clearpoints, $i);
        }
        $i++;
    }
    if (!count(array_diff(array(0,1,2), $clearpoints))) {
        return true;
    }
    if (!count(array_diff(array(3,4,5), $clearpoints))) {
        return true;
    }
    if (!count(array_diff(array(6,7,8), $clearpoints))) {
        return true;
    }
    if (!count(array_diff(array(0,3,6), $clearpoints))) {
        return true;
    }
    if (!count(array_diff(array(1,4,7), $clearpoints))) {
        return true;
    }
    if (!count(array_diff(array(2,5,8), $clearpoints))) {
        return true;
    }
    if (!count(array_diff(array(0,4,8), $clearpoints))) {
        return true;
    }
    if (!count(array_diff(array(2,4,6), $clearpoints))) {
        return true;
    }
    return false;
}
if (judgeBingoClear()) {
    $uuid = $_COOKIE["uuid"];
    try {
        $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = $db->prepare('select id from ' . BINGO_TABLE . ' where uuid = ?');
        $query->execute([$uuid]);
        if ($query->rowCount() == 0) {
            $stmt = $db->prepare('insert into ' . BINGO_TABLE . ' (uuid, created) values (:uuid, now())');
            $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
            $stmt->execute();
        }
        $query = $db->prepare('select id from ' . BINGO_TABLE . ' where uuid = ?');
        $query->execute([$uuid]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $id = $result[0]["id"];
        $db=null;
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
  <!-- ヘッダー -->
  <?php
  $dom = new DOMDocument();
  $dom->loadHTML(mb_convert_encoding(file_get_contents("./component/headtag.html"), 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS'));
  $dom->removeChild($dom->doctype);
  $dom->replaceChild($dom->firstChild->firstChild, $dom->firstChild);
  echo mb_convert_encoding($dom->saveHTML(), 'utf-8', 'HTML-ENTITIES');
  ?>
  <body style="padding-top:70px;">
    <!-- ナビゲーションバー -->
    <?php
      $dom = new DOMDocument();
      $dom->loadHTML(mb_convert_encoding(file_get_contents("./component/navbar.html"), 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS'));
      $dom->getElementById("top")->setAttribute("class", "active");
      $dom->removeChild($dom->doctype);
      $dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);
      echo mb_convert_encoding($dom->saveHTML(), 'utf-8', 'HTML-ENTITIES');
    ?>

    <div class="certification container">
      <img src="./images/site_materials/certification_v4.png" style="width:100%;">
      <p class="certification-top-text" style="text-decoration:underline;">
        認定証
      </p>
      <p class="certification-content">
        <?php echo $_COOKIE["name"]; ?>様<br />
        クリアID : <?php if(isset($id)){echo  str_pad($id, 4, 0, STR_PAD_LEFT);}else{echo "IDが存在しません．";} ?><br /><br />
        あなたは「フォト俳句の杜らりぃ」において，見事「ラリーBINGO」をクリアされました。
        感謝の意をもってここに表彰いたします．
      </p>
    </div>
    <div class="container">
      <p>景品を受け取るにはイオンモール伊丹1階インフォメーションにて，この画面を表示してください．</p>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>


  </body>
</html>
