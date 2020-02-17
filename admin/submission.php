<?php
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", "/home/s2-lab/log/phperror.log");
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');

require("../dbinfo.php");
require("../resizer.php");
require("../image_rotation.php");

$message = "";

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
if (isset($_POST["gps"]) && strpos($_POST["gps"], ",")) {
    $textArray = explode(",", $_POST["gps"]);
    $lat = (float) $textArray[0];
    $lng = (float) $textArray[1];
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
    $uuid_dir = sprintf('../images/uploads/%s/', $uuid);
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
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理者専用フォーム</title>

    <!-- Bootstrap -->
    <link href="../css/blue-haiku.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/my-bootstrap.css" />
  </head>
  <body>
    <div class="container">
      <div class="row">
        <h2><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></h2>
        <h3><a href="./form.php">フォームに戻る</a></h3>
      </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.min.js"></script>
    </body>
</html>
