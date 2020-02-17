<?php
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", "/home/s2-lab/log/phperror.log");
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');
error_reporting(E_ALL ^ E_NOTICE);
require("dbinfo.php");
require("resizer.php");
require("image_rotation.php");

$message = "";
$submessage = "";

//
function judgeBingo($lat, $lng){
  # This array means the regions to clear rallypoints(Itami hakkei).
  # The all regions are squares.
  # Each element means end of north, east, south, and west of the squares.
  $rallypoints = array(
    'aramaki' => array('north' => 34.813500, 'east' => 135.388500, 'south' => 34.810000, 'west' => 135.384000),
    'midorigaokakoen' => array('north' => 34.796000, 'east' => 135.410500, 'south' => 34.793000, 'west' => 135.406000),
    'itamiryokuchi' => array('north' => 34.796000, 'east' => 135.419500, 'south' => 34.786000, 'west' => 135.410500),
    'koyaikekoen' => array('north' => 34.793000, 'east' => 135.399500, 'south' => 34.785000, 'west' => 135.38800),
    'eaonmall' => array('north' => 34.783267, 'east' => 135.4257500, 'south' => 34.780000, 'west' => 135.421826),
    'itamikuko' => array('north' => 34.798500, 'east' => 135.458000, 'south' => 34.769320, 'west' => 135.425750),
    'kotobagura' => array('north' => 34.786000, 'east' => 135.420000, 'south' => 34.781660, 'west' => 135.413500),
    'chojuzo' => array('north' => 34.781660, 'east' => 135.420000, 'south' => 34.778850, 'west' => 135.413500),
    'gogadukakofun' => array('north' => 34.769000, 'east' => 135.418500, 'south' => 34.7639000, 'west' => 135.411100)
  );

  $clearpoint = null;
  foreach ($rallypoints as $key =>$point) {
    # code...
    if($lat < $point['north'] && $lat > $point['south'] && $lng > $point['west'] && $lng < $point['east']){
       $clearpoint = $key;
    }
  }
  return $clearpoint;
}

//ペンネーム
if (isset($_POST["name"])) {
    $name = (strlen($_POST["name"]) > 0) ? $_POST["name"] : "名無し";
    setcookie("name", ($name != "名無し") ? $_POST["name"] : "", time() + 60*60*24*60);
} else {
    $message = "情報が不正です．";
    return;
}

//生年月日
if (isset($_POST["birthday"])) {
    $birthday = (strlen($_POST["birthday"]) > 0) ? $_POST["birthday"] : "1900-01-01";
    setcookie("birthday", $birthday, time() + 60*60*24*60);
} else {
    $message = "生年月日を入力してください．";
    return;
}

//地域
if (isset($_POST["address"])) {
    $address = (strlen($_POST["address"]) > 0) ? $_POST["address"] : "";
    setcookie("address", $address, time() + 60*60*24*60);
} else {
    $message = "お住いの地域を入力してください．";
    return;
}

//あいことば
if (isset($_POST["aikotoba"])) {
    $aikotoba = $_POST["aikotoba"];
    setcookie("aikotoba", $aikotoba, time() + 60*60*24*60);
} else {
    //あいことばと俳句が空ならデータベースに保存されない．
    $message = "あいことばがありません．";
    return;
}

//俳句
if (isset($_POST["haiku"])) {
    $haiku = $_POST["haiku"];
} else {
    //あいことばと俳句が空ならデータベースに保存されない．
    $message = "俳句情報がありません．";
    return;
}

//位置情報
if (isset($_POST["lat"]) && isset($_POST["lng"])) {
    $lat = $_POST["lat"];
    $lng = $_POST["lng"];
    $rallypoint = judgeBingo($lat, $lng);
    if($rallypoint != null){
      // echo "<br /><br /><br />";
      // var_dump($rallypoint);
      setcookie($rallypoint, "aaa", time() + 60*60*24*60);
    }
} else {
    $lat = null;
    $lng = null;
}

//異常な画像ファイル検知とデータベース登録の例外を投げる．
try {
    /*
    画像のアップロード処理
    */
    // 未定義である・複数ファイルである・$_FILES Corruption 攻撃を受けた
    // どれかに該当していれば不正なパラメータとして処理する
    if (!isset($_FILES['upfile']['error']) || !is_int($_FILES['upfile']['error'])) {
        throw new RuntimeException('パラメータが不正です');
    }

    // $_FILES['upfile']['error'] の値を確認
    switch ($_FILES['upfile']['error']) {
        case UPLOAD_ERR_OK: // OK
            break;
        case UPLOAD_ERR_NO_FILE:   // ファイル未選択
            throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過 (設定した場合のみ)
            throw new RuntimeException('ファイルサイズが大きすぎます');
        default:
            throw new RuntimeException('その他のエラーが発生しました');
    }

    // $_FILES['upfile']['mime']の値はブラウザ側で偽装可能なので
    // MIMEタイプに対応する拡張子を自前で取得する
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if (!$ext = array_search(
        finfo_file($finfo, $_FILES['upfile']['tmp_name']),
        array(
            'jpg' => 'image/jpeg'
        ),
        true
    )) {
        //$extが空なら例外が投げられる．
        throw new RuntimeException('ファイル形式が不正です．jpgファイルのみ対応しています．');
    }
    finfo_close($finfo);

    $uuid = isset($_COOKIE["uuid"]) ? $_COOKIE["uuid"] : "no_uuid";
    $photoid = md5(uniqid(mt_rand(), true));
    $uuid_dir = sprintf('./images/uploads/%s/', $uuid);
    $pic_path = sprintf($uuid_dir . 'pic-' . '%s.%s', $photoid, $ext);
    $small_thumb_path = sprintf($uuid_dir . 'st-' . '%s.%s', $photoid, $ext);
    $large_thumb_path = sprintf($uuid_dir . 'lt-' . '%s.%s', $photoid, $ext);

    if (!is_dir($uuid_dir)) {
        if (!mkdir($uuid_dir)) {
            throw new RuntimeException('ディレクトリ作成に失敗しました．:' . $uuid_dir);
        }
        chmod($uuid_dir, 0777);
    }
    if (is_dir($uuid_dir)) {
        if (!move_uploaded_file(
        $_FILES['upfile']['tmp_name'],
        $pic_path
    )) {
            throw new RuntimeException('ファイル保存時にエラーが発生しました');
        }
    }

    orientationFixedImage($pic_path, $pic_path);
    // ファイルのパーミッションを確実に0644に設定する
    chmod($pic_path, 0644);

    // 120x120以内にリサイズ
    if (!$resize120 = roundresizer(120, $pic_path, $small_thumb_path)) {
        throw new RuntimeException('画像リサイズに失敗しました．(120)');
    }
    // 360x360以内にリサイズ
    if (!$resize360 = roundresizer(360, $pic_path, $large_thumb_path)) {
        throw new RuntimeException('画像リサイズに失敗しました．(360)');
    }

    /*
    データベース処理
    */
    //connect
    $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //insert
    mb_convert_variables("EUC-JP", "UTF-8", $uuid, $name, $address, $aikotoba, $haiku, $photoid);
    //echo '<br /><br /><br /><br />insert into ' . DB_TABLE . ' (uuid, name, birthday, address, aikotoba, haiku, photoid, lat, lng, created) values (:uuid, :name, :birthday, :address, :aikotoba, :haiku, :photoid, :lat, :lng, now())';
    $stmt = $db->prepare('insert into ' . DB_TABLE . ' (uuid, name, birthday, address, aikotoba, haiku, photoid, lat, lng, created) values (:uuid, :name, :birthday, :address, :aikotoba, :haiku, :photoid, :lat, :lng, now())');
    $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':birthday', $birthday, PDO::PARAM_INT);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':aikotoba', $aikotoba, PDO::PARAM_STR);
    $stmt->bindParam(':haiku', $haiku, PDO::PARAM_STR);
    $stmt->bindParam(':photoid', $photoid, PDO::PARAM_STR);
    if ($lat != null && $lng != null) {
        mb_convert_variables("EUC-JP", "UTF-8", $lat, $lng);
        $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
        $stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
    } else {
      $stmt->bindParam(':lat', $lat, PDO::PARAM_NULL);
      $stmt->bindParam(':lng', $lng, PDO::PARAM_NULL);
        $submessage = "位置情報は取得しませんでした．";
    }
    //$stmt->bindParam('created', strtotime(date ("Y-m-d H:i:s")), PDO::PARAM_DATE);
    $stmt->execute();
    /*
    HTML中に出力するメッセージ
    */
    $message = "投稿完了！ありがとうございました！";
} catch (RuntimeException $e) {
    $message = $e->getMessage();
    echo $message;
    return;
} catch (PDOException $e) {
    $message = $e->getMessage();
    echo $message;
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
  <body>
    <!-- ナビゲーションバー -->
    <?php
      $dom = new DOMDocument();
      $dom->loadHTML(mb_convert_encoding(file_get_contents("./component/navbar.html"), 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS'));
      $dom->getElementById("top")->setAttribute("class", "active");
      $dom->removeChild($dom->doctype);
      $dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);
      echo mb_convert_encoding($dom->saveHTML(), 'utf-8', 'HTML-ENTITIES');
    ?>
    <div class="container" style="padding:100px 0">
      <h2><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></h2>
      <h2><?php echo htmlspecialchars($submessage, ENT_QUOTES, 'UTF-8'); ?></h2>
      <h3><a href="./">トップへ戻るにはこちら</a></h3>
    </div>
    </body>
</html>
