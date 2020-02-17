<?php
ini_set("display_errors", 0);
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
      $dom->getElementById("qa")->setAttribute("class", "active");
      $dom->removeChild($dom->doctype);
      $dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);
      echo mb_convert_encoding($dom->saveHTML(), 'utf-8', 'HTML-ENTITIES');
    ?>
    <!-- アコーディオンQ and A -->
    <div class="container" style="margin-top:50px;margin-bottom:100px;">
      <div class="row">
        <h2>端末操作方法</h2>
        <ul style="list-style-type: none">
          <li>
            <a data-toggle="collapse" href="#content1"><h4>位置情報が取得できないエラー表示がでる。</h4></a>
            <div class="collapse collapse-menu" id="content1">
              <p>
                この場合、ブラウザや端末の設定を変更していただく必要がありますので、端末の「位置情報サービス」の設定が有効になっているか、下記手順でご確認ください。
              </p>
              <p>
                ■iPhone (iOS6以降) の場合<br />
                <ol>
                  <li>ホーム画面の [設定] をタップします</li>
                  <li>[プライバシー] をタップします</li>
                  <li>[プライバシー] をタップします</li>
                  <li>[位置情報サービス] をタップします</li>
                  <li>[位置情報サービス]が「オフ」の場合は「オン」に、[Safariのサイト]の項目が「許可しない」になっている場合は「使用中のみ許可」に設定します</li>
                </ol>
                <br />
                <p>※Safari以外のインターネットブラウザ（Chromeなど）を使用する場合もSafari と同様に設定します</p>
                <p>※上記操作で改善しない場合には、以下の操作をお試しください</p>
                <ol>
                  <li>ホーム画面の [設定] をタップします</li>
                  <li>[一般] をタップします</li>
                  <li>[リセット] をタップします</li>
                  <li>[位置情報とプライバシーをリセット] をタップします</li>
                  <li>[設定をリセット] をタップします</li>

                </ol>
              </p>
              <p>
                ■Android (OS4.*以降)の場合
                <ol>
                  <li>端末の設定アプリ[歯車アイコン]をタップします</li>
                  <li>[位置情報サービス]もしくは[位置情報アクセス]をタップします</li>
                  <li>[位置情報にアクセス]をONに設定し、[GPS機能]をタップしてチェックをつけます</li>
                </ol>
              </p>
              <p>
                ■Android (OS2.3.7.*)の場合
                <ol>
                  <li>端末の設定アプリ[歯車アイコン]をタップします</li>
                  <li>[現在地情報とセキュリティーの設定]</li>
                  <li>[無線ネットワークを使用]、[GPS機能を使用]をタップしてチェックをつけます</li>
                </ol>
              </p>
              <p>
                ※以上の設定を行った上で投稿画面で「ご利用の環境では位置情報の取得が許可されていません．」と表示される場合は，使用しているインターネットブラウザが位置情報取得を許可していない可能性があります．インターネットブラウザの
                設定画面をご確認ください．
              </p>
            </div>
          </li>
          <li>
            <a data-toggle="collapse" href="#content2"><h4>ふぉと俳句投稿方法</h4></a>
            <p class="collapse collapse-menu" id="content2">専用HP内、〔俳句を詠んでみる〕をタップし、投稿してください。初回のみ、ペンネーム、あいことば、生年月日、お住まいの地域の設定が必要です。</p>
          </li>
          <li>
            <a data-toggle="collapse" href="#content3"><h4>自分の投稿を確認する方法</h4></a>
            <p class="collapse collapse-menu" id="content3">専用HP内、〔あなたの投稿〕よりご自身が投稿された作品を閲覧することができます。</p>
          </li>
          <li>
            <a data-toggle="collapse" href="#content4"><h4>他の人の投稿を確認する方法</h4></a>
            <p class="collapse collapse-menu" id="content4">トップページ内、〔マップ〕をタップし、ふぉと俳句投稿ポイントにピンが表示されています。ピンにカーソルを合わすと、ふぉと俳句を閲覧することができます。また、〔最新の投稿〕ページにも、ランダムで作品が表示されます。</p>
          </li>
        <ul/>
      </div>
      <div class="row">
        <h2>応募方法</h2>
        <ul style="list-style-type: none">
          <li>
            <a data-toggle="collapse" href="#content5"><h4>スマートフォンを持っていません。別途、参加方法ありますか？</h4></a>
            <p class="collapse collapse-menu" id="content5">申し訳ございません。ラリーBINGOはGPS機能付の端末ご使用時のみ参加可能です。ふぉと俳句の投稿は、専用応募用紙を使い、伊丹八景をテーマに作品応募いただけます。</p>
          </li>
          <li>
            <a data-toggle="collapse" href="#content6"><h4>デジタルカメラやPC内写真を投稿できますか？</h4></a>
            <p class="collapse collapse-menu" id="content6">投稿可能です。投稿の位置情報がとれない場合は、ラリーBINGOのラリーポイントは獲得できません。</p>
          </li>
        </ul>
      </div>
      <div class="row">
        <h2>表彰について</h2>
        <ul style="list-style-type: none">
          <li>
            <a data-toggle="collapse" href="#content7"><h4>ふぉと俳句表彰基準は？</h4></a>
            <p class="collapse collapse-menu" id="content7">写真と俳句の組み合わせで、テーマに則り、自由にのびのびと表現豊かな作品を表彰します。季語・定型は不問です。</p>
          </li>
          <li>
            <a data-toggle="collapse" href="#content8"><h4>入賞作品の発表の方法は？</h4></a>
            <p class="collapse collapse-menu" id="content8">ふぉと俳句の杜らりぃホームページ、イオンモール伊丹１階インフォメーション、伊丹市立図書館「ことば蔵」にて掲示発表（11月７日予定）</p>
          </li>
          <li>
            <a data-toggle="collapse" href="#content9"><h4>入賞時の副賞の受取方法</h4></a>
            <p class="collapse collapse-menu" id="content9">イオンモール伊丹１階インフォメーションにて受け渡し。応募時に設定した「ペンネーム」「あいことば」「生年月日」にてご本人確認をさせていただき、お渡しさせていただきます。</p>
          </li>
        </ul>
      </div>
      <div class="row">
        <h2>ラリーBINGO</h2>
        <ul style="list-style-type: none">
          <li>
            <a data-toggle="collapse" href="#content10"><h4>ラリーポイントはどこですか？</h4></a>
            <p class="collapse collapse-menu" id="content10">いたみ八景（みやのまえ文化の郷、長寿蔵、御願塚古墳、伊丹空港、昆陽池公園、緑ヶ丘公園、伊丹緑地、荒牧バラ公園）＋イオンモール伊丹の全９箇所にラリーポイントを設けています。</p>
            <li>
              <a data-toggle="collapse" href="#content11"><h4>ラリーBINGO達成条件</h4></a>
              <p class="collapse collapse-menu" id="content11">スマホの位置情報設定をONにし、ふぉと俳句を投稿。縦・横・斜めのいずれか一列ラリーポイントが完成すればビンゴクリア。認定証が表示されます。</p>
            </li>
            <li>
              <a data-toggle="collapse" href="#content12"><h4>ラリーBINGO景品交換</h4></a>
              <p class="collapse collapse-menu" id="content12">認定証をご呈示で イオンモール伊丹 スイーツストリートで使える「からだにやさしいスイーツ」引換券をプレゼント。（お一人さま１回限り）イオンモール １階 インフォメーションでお渡し。</p>
            </li>
          </li>
        </ul>
      </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
  </body>
</html>
