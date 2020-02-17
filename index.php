<?php
ini_set("display_errors", 0);
error_reporting(E_ALL ^ E_NOTICE);
require("dbinfo.php");
require("path_utility.php");

if (!isset($_COOKIE['uuid'])) {
    setcookie("uuid", md5(uniqid(mt_rand(), true)), time() + 60*60*24*180);
}

try {
    //connect
    $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query('select * from ' . DB_TABLE . ' order by created desc limit 6');
    $new_haikus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare('select * from ' . DB_TABLE . ' where (uuid = ?) order by created desc');
    $stmt->bindParam(1, $_COOKIE["uuid"]);
    $stmt->execute();
    $my_haikus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db=null;
} catch (PDOException $e) {
    echo $e->getMessage();
}

function bingoview($spotname)
{
    if (isset($_COOKIE[$spotname])) {
        echo '<img class="bingo-img" src="images/site_materials/complete.png"/>';
    } else {
        echo '<img class="bingo-img" src="images/site_materials/notcomplete.png" style="opacity:0.5"/>';
    }
}

function judgeBingo($lat, $lng)
{
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
        if ($lat < $point['north'] && $lat > $point['south'] && $lng > $point['west'] && $lng < $point['east']) {
            $clearpoint = $key;
        }
    }
    return $clearpoint;
}

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
  <body style="margin-bottom:70px">
    <div id="tracker" style="position:absolute;visibility:hidden;">
      <script type="text/javascript" src="https://hai-ku.net/lunalys/analyzer/tracker.js"></script>
      <noscript><img src="https://hai-ku.net/lunalys/analyzer/write.php?guid=ON&act=img" width="0" height="0" alt="tracker"></noscript>
    </div>
    <!-- ナビゲーションバー -->
    <?php
      $dom = new DOMDocument();
      $dom->loadHTML(mb_convert_encoding(file_get_contents("./component/navbar.html"), 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS'));
      $dom->getElementById("top")->setAttribute("class", "active");
      $dom->removeChild($dom->doctype);
      $dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);
      echo mb_convert_encoding($dom->saveHTML(), 'utf-8', 'HTML-ENTITIES');
    ?>

    <div class="img-responsive top-image jumbotron">
    </div>

    <div class="container" style="padding:20px 0">
      <div class="panel panel-primary">
        <div class="panel-heading">
          インフォメーション
        </div>
        <div class="panel-body" style="height: 160px;overflow: scroll;">
          <p>優秀作品発表！<a href="./contents/awards.pdf">こちらから</a></p>
          <hr />
          <p>「ふぉと俳句の杜らりぃ」は終了しました。<br />入賞作品は2017年11月7日に当サイト、イオンモール伊丹インフォメーションおよびことば蔵にて発表予定です。<br >たくさんの投稿ありがとうございました！(2017/10/23)</p>
          <hr />
          <p class="text-danger">【注意】ブラウザの履歴(Cookie)を消すとリセットされて最初の状態からやり直しになってしまいます。</p>
          <hr />
          <p>「ふぉと俳句の杜らりぃ」特設WEBサイトオープンしました！(2017/09/23)</p>
          <ul>
            <li><a href="http://itami-aeonmall.com/photohaiku/">初めての方はこちら</a><br /></li>
            <li><a href="https://hai-ku.net/qa.php">よくある質問Q&amp;Aはこちら</a></li>
            <li><a href="http://www.city.itami.lg.jp/ikkrwebBrowse/material/files/group/51/0001696_001.pdf">伊丹八景マップはこちらから</a></li>
          </ul>
        </div>
      </div>
      <ul class="nav nav-tabs" style="margin-bottom:15px">
        <li class="nav-item active">
          <a class="nav-link" href="#everyone-post" data-toggle="tab">みんなの投稿</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#your-post" data-toggle="tab">あなたの投稿</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#haiku-map" data-toggle="tab" id="map-tab">マップ</a>
        </li>
      </ul>

      <!-- みんなの投稿 -->
      <div class="tab-content">
        <div class="tab-pane active" id="everyone-post">
          <div id="header" class="container">
            <h2>最新の投稿</h2>
          </div>
          <div  class="container">
            <div class="row">
              <div class="row row-eq-height" id="haikuposts">
              <?php
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
                    echo mb_convert_encoding($dom->saveHTML(), 'utf-8', 'HTML-ENTITIES');
                }
              ?>
              </div>
              <div class="row" style="text-align:center;">
                <p id="loading" style="display:none;">読み込み中...</p>
                <button type="button" class="btn btn-primary" style="text-align:center;" id="more">もっと読む</button>
              </div>
            </div>
          </div>

          <div id="footer" class="container">
            <!-- フッタ -->
          </div>
        </div>

        <!-- あなたの投稿 -->
        <div class="tab-pane" id="your-post">
          <div id="header" class="container">
          </div>
          <h2>ビンゴクリア状況</h2>
          <?php if (judgeBingoClear()) {
                  echo '<h2 style="text-align:center;">ビンゴクリアです！<br /><a href="./bingo.php">ビンゴ認定証</a></h2>';
              } ?>
          <div class="container" style="align:center;">
            <table class="row table table-bordered" style="width: 100%;table-layout:fixed;text-align:center;margin-left:auto;margin-right:auto;">
              <tr>
                <td>
                  <div class="bingo-top-text">荒牧バラ公園</div>
                  <?php bingoview("aramaki");?>
                </td>
                <td>
                  <div class="bingo-top-text">緑ヶ丘公園</div>
                  <?php bingoview("midorigaokakoen");?>
                </td>
                <td>
                  <div class="bingo-top-text">伊丹緑地</div>
                  <?php bingoview("itamiryokuchi");?>
                </td>
              </tr>

              <tr>
                <td>
                  <div class="bingo-top-text">昆陽池公園</div>
                  <?php bingoview("koyaikekoen");?>
                </td>
                <td>
                  <div class="bingo-top-text">イオンモール伊丹</div>
                  <?php bingoview("eaonmall");?>
                </td>
                <td>
                  <div class="bingo-top-text">伊丹空港</div>
                  <?php bingoview("itamikuko");?>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="bingo-top-text">ことば蔵(みやのまえ文化の郷)</div>
                  <?php bingoview("kotobagura");?>
                </td>
                <td>
                  <div class="bingo-top-text">長寿蔵</div>
                  <?php bingoview("chojuzo");?>
                </td>
                <td>
                  <div class="bingo-top-text">御願塚古墳</div>
                  <?php bingoview("gogadukakofun");?>
                </td>
              </tr>
            </table>
          </div>
          <h2>すべての投稿</h2>
          <div class="container">
            <div class="row">
              <?php
                foreach ($my_haikus as $haiku) {
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
                    echo mb_convert_encoding($dom->saveHTML(), 'utf-8', 'HTML-ENTITIES');
                }
              ?>
            </div>
          </div>
          <div id="footer" class="container">
            <!-- フッタ -->
          </div>
        </div>
        <div class="tab-pane" id="haiku-map">
          <div id="header" class="container">
            <h2>俳句の名所</h2>
          </div>
          <div class="container">
            <div class="row">
              <div id="map" style="margin-bottom: 50px"></div>
            </div>
          </div>
          <div id="footer" class="container">
            <!-- フッタ -->
          </div>
        </div>
      </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAnIrF-JTm7K6p6of_OdrnfvkYVbr3WwuY"></script>
    <script src="js/moment.min.js"></script>
    <script>
    // 地図を挿入したい要素を取得
    var canvas = document.getElementById('map') ;
    // 中心の位置座標を指定
    var latlng = new google.maps.LatLng(34.7821846,135.4201047);  //イオンモール伊丹
    var mapOptions = {
      zoom: 13,
      center: latlng,
    };
    // [canvas]に、[mapOptions]の内容の、地図のインスタンス([map])を作成する
    var map = new google.maps.Map( canvas , mapOptions ) ;

    //マーカー配置
    //マーカーをクリックしたときinfoWindowが表示される
    var infoWindow = new google.maps.InfoWindow;

    // Change this depending on the name of your PHP or XML file
    downloadUrl('https://hai-ku.net/dbtoxml.php', function(data) {
      var xml = data.responseXML;
      var markers = xml.documentElement.getElementsByTagName('marker');
      Array.prototype.forEach.call(markers, function(markerElem) {
        var name = markerElem.getAttribute('name');
        var haiku = markerElem.getAttribute('haiku');
        var imgpath = markerElem.getAttribute('imgpath');
        var point = new google.maps.LatLng(
            parseFloat(markerElem.getAttribute('lat')),
            parseFloat(markerElem.getAttribute('lng')));

        var infowincontent = document.createElement('div');
        var img = document.createElement('img');
        img.setAttribute("src", imgpath);
        img.setAttribute("style", "display: block;margin-left: auto;margin-right: auto;");
        infowincontent.appendChild(img);

        infowincontent.appendChild(document.createElement('br'));

        var haikutext = document.createElement('text');
        haikutext.textContent = haiku;
        haikutext.setAttribute("style", "display: block;text-align: center;");
        infowincontent.appendChild(haikutext);

        infowincontent.appendChild(document.createElement('br'));

        var poster = document.createElement('text');
        poster.textContent = name +"さんの投稿";
        poster.setAttribute("style", "display: block;text-align: right;font-size: xx-small;");
        infowincontent.appendChild(poster);

        var marker = new google.maps.Marker({
          map: map,
          position: point
        });
        marker.addListener('click', function() {
          infoWindow.setContent(infowincontent);
          infoWindow.open(map, marker);
        });
      });
    });

    function downloadUrl(url,callback) {
      var request = window.ActiveXObject ?
        new ActiveXObject('Microsoft.XMLHTTP') :
        new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }
    function doNothing() {}
    google.maps.event.addDomListener(window, 'resize', function(){
      map.panTo(latlng);//地図のインスタンス([map])
    });
    </script>

    <!--もっと見るの実装-->
    <script>
      var date = moment().format('YYYY-MM-DD H:mm:ss');
      var page = 1;
      $(function(){
        $('#more').click(function(){
          $('#loading').show();
          $.get('more.php', {
            now: date,
            page: page
          }, function(results){
            $('#loading').hide();
            $(results).appendTo('#haikuposts');
            page = page+1;
          })
        });
      });
    </script>

    <!-- マップ再描画 -->
    <script>
    $("#map-tab").on('shown.bs.tab', function() {
      	/* Trigger map resize event */
    	  google.maps.event.trigger(map, 'resize');
        map.setCenter(latlng);
    });
    </script>

  </body>
</html>
