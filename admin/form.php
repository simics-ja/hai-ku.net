<?php
ini_set("display_errors", 1);

$message = "";
$null_param = 0;

if (isset($_COOKIE["name"]) && isset($_COOKIE["aikotoba"]) && isset($_COOKIE["birthday"]) && isset($_COOKIE["address"])) {
    $name = $_COOKIE["name"];
    $aikotoba = $_COOKIE["aikotoba"];
    $birthday = $_COOKIE["birthday"];
    $address = $_COOKIE["address"];
}
?>

<!DOCTYPE html>
<html lang="ja">
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
    <!-- アップロードフォーム -->
    <div class="container">
      <form action="submission.php" method="post" enctype="multipart/form-data" id="haikuform">
        <div class="form-group">
          <div class="row">
            <h4 class="col-xs-12 col-sm-12">1. 情報を入力してください．</h4>
          </div>
          <div class="row">
            <div class="col-xs-ofsset-1 col-xs-11 col-sm-ofsset-1 col-sm-11">
              <label>ペンネーム</label>
              <input type="text" name="name" placeholder="例)松尾芭蕉" class="form-control" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"/>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-ofsset-1 col-xs-4 col-sm-ofsset-1 col-sm-4">
              <label for="exampleFormControlSelect1">生年月日</label>
              <input type="date" name="birthday" value="<?php echo htmlspecialchars($birthday, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" required aria-required="true"/>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-ofsset-1 col-xs-11 col-sm-ofsset-1 col-sm-11">
              <label>お住いの地域</label>
              <input type="text" name="address" placeholder="例)兵庫県伊丹市" class="form-control" value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>"/>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-ofsset-1 col-xs-11 col-sm-ofsset-1 col-sm-11">
              <label>あいことば</label>
              <input type="text" name="aikotoba" placeholder="いろはにほへと" class="form-control" value="<?php echo htmlspecialchars($aikotoba, ENT_QUOTES, 'UTF-8'); ?>" required aria-required="true"/>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-ofsset-1 col-xs-11 col-sm-ofsset-1 col-sm-11">
              <label>位置情報</label>
              <input type="text" name="gps" placeholder="34.781579, 135.423835(全角「、」ではなく半角「,」)" class="form-control" required aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="row">
            <h4 class="col-xs-12 col-sm-12">2. 写真を選択してください．</h4>
          </div>
          <div class="row">
            <div class="col-xs-ofsset-1 col-xs-11 col-sm-ofsset-1 col-sm-11">
              <input type="file" accept="image/jpg;capture=camera" name="upfile" class="form-control" required aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="row">
            <h4 class="col-xs-12 col-sm-12">3. 俳句をどうぞ！</h4>
          </div>
          <div class="row">
            <div class="col-xs-ofsset-1 col-xs-11 col-sm-ofsset-1 col-sm-11">
              <input class="form-control" type="text" name="haiku" placeholder="例)松島や ああ松島や 松島や" required aria-required="true"/>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-2 col-sm-2" style="margin-top: 10px">
            <input class="btn btn-primary" type="submit" id="submit_button" value="投稿"/>
          </div>
        </div>
      </form>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.min.js"></script>
  </body>
</html>
