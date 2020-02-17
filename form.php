<?php
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", "/home/s2-lab/log/phperror.log");
error_reporting(E_ALL ^ E_NOTICE);
if (isset($_COOKIE["name"]) && isset($_COOKIE["aikotoba"]) && isset($_COOKIE["birthday"]) && isset($_COOKIE["address"])) {
    $name = $_COOKIE["name"];
    $aikotoba = $_COOKIE["aikotoba"];
    $birthday = $_COOKIE["birthday"];
    $address = $_COOKIE["address"];
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
    <!-- アップロードフォーム -->
    <div class="container" style="padding:100px 20px">
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
            <div class="container">
              <p>本名のご使用はお避けください。</p>
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
            <div class="container">
              <p>これらの情報は入賞時のご本人確認に使用します。</p>
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
          <div class="row">
            <div class="container">
              <p>写真は被写体の肖像権やプライバシーに配慮し、投稿の了解を得てください。<br />画像形式はJPEGのみ対応しています。</p>
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
            <input class="btn btn-primary" type="submit" onclick="return false;" id="submit_button" value="投稿"/>
          </div>
        </div>
      </form>
      <div class="row">
        <div class="container" style="margin-top: 50px;">
          <hr />
          <ul>
            <li>ラリーBINGOご参加には、端末の位置情報取得許可（GPS機能をON）が必要です。取得した位置情報は、神戸大学 塚本・寺田研究室の管理下のもと、本企画運営及び、研究活動に使用いたします。なお、位置情報には、利用者の個人を特定する情報は含まれていません。</li>
            <li>お手持ちのスマートフォン端末の機種毎に性能が異なり、GPSに大きな誤差が出る場合がございます。</li>
            <li>当サイトではCookieを使ってお客様の投稿情報を管理しています。Cookieとはサイトが利用者のパソコン等のブラウザに情報を保存し、あとで取り出すことができる技術のひとつです。なお、利用者は当ホームページを利用することでCookieの使用に許可を与えたものとみなします。</li>
            <li>応募作品は返却いたしません。投稿作品の原著作権は作者に帰属しますが、イオンモール㈱および、神戸大学　塚本・寺田研究室が入賞・入選作品の発表や作品展・ホームページ・印刷物に無償で利用・提供できることとします。</li>

          </ul>
        </div>
      </div>
    </div>
    <script>
    alert("キャンペーン参加には現在位置情報が必要となります。")
    // Geolocation APIに対応している
    if (navigator.geolocation) {

    } else {
      // Geolocation APIに対応していない
      alert("お使いの端末では位置情報が取得できません。");
    }
    console.log("Now trying to get the location.");
    navigator.geolocation.getCurrentPosition(
      // 取得成功した場合
      function(position) {
        var lat = document.createElement("input");
        lat.setAttribute('type', 'hidden');
        lat.setAttribute('name', 'lat');
        lat.setAttribute('value', position.coords.latitude);
        document.getElementById("haikuform").appendChild(lat);

        var lng = document.createElement("input");
        lng.setAttribute('type', 'hidden');
        lng.setAttribute('name', 'lng');
        lng.setAttribute('value', position.coords.longitude);
        document.getElementById("haikuform").appendChild(lng);
        console.log("Success.");
        document.getElementById("submit_button").removeAttribute("onclick");
      },
      // 取得失敗した場合
      function(error) {
        switch (error.code) {
          case 1: //PERMISSION_DENIED
            alert("ご利用の環境では位置情報の取得が許可されていません。");
            break;
          case 2: //POSITION_UNAVAILABLE
            alert("何らかの理由により現在位置情報が取得できませんでした。");
            break;
          case 3: //TIMEOUT
            alert("タイムアウトにより位置情報を取得できませんでした。");
            break;
          default:
            alert("位置情報取得エラー(エラーコード:" + error.code + ")");
            break;
        }

        console.log("Failure.");
        document.getElementById("submit_button").removeAttribute("onclick");
      }
    );
    </script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
