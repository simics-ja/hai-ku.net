
console.log("getlocation");
var permission;
// Geolocation APIに対応している
if (navigator.geolocation) {
  permission = true;
  if (!confirm("キャンペーン参加には現在位置情報が必要となります。\n位置情報の取得を許可しますか？")) {
    alert("位置情報を取得しません．");
    permission = false;
  }
} else {
  // Geolocation APIに対応していない
  alert("お使いの端末では位置情報が取得できません。\n(キャンペーン参加には位置情報が必要です．)");
  permission = false;
}

if (permission) {
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
      document.getElementById("submit_button").onclick = true;
    },
    // 取得失敗した場合
    function(error) {
      switch (error.code) {
        case 1: //PERMISSION_DENIED
          alert("ご利用の環境では位置情報の取得が許可されていません．");
          break;
        case 2: //POSITION_UNAVAILABLE
          alert("何らかの理由により現在位置情報が取得できませんでした．");
          break;
        case 3: //TIMEOUT
          alert("タイムアウトにより位置情報を取得できませんでした．");
          break;
        default:
          alert("位置情報取得エラー(エラーコード:" + error.code + ")");
          break;
      }

      console.log("Failure.");
      document.getElementById("submit_button").onclick = "return true";
    }
  );
} else {
  console.log("Not permitted.");
  document.getElementById("submit_button").onclick = "return true";
}
