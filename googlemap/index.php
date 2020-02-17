<!DOCTYPE html>
<html>
  <head>
    <style>
       #map {
        height: 600px;
        width: 100%;
       }
    </style>
  </head>
  <body>
    <h3>My Google Maps Demo</h3>
    <div id="map"></div>
    <script>
    var customLabel = {
      Laboratory: {
        label: 'R'
      },
      WardOffice: {
        label: 'B'
      }
    };

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: new google.maps.LatLng(34.719836, 135.234427),
          zoom: 14
        });
        var infoWindow = new google.maps.InfoWindow;

          // Change this depending on the name of your PHP or XML file
          downloadUrl('http://hai-ku.net/dev/googlemap/dbtoxml-dumper.php', function(data) {
            var xml = data.responseXML;
            var markers = xml.documentElement.getElementsByTagName('marker');
            Array.prototype.forEach.call(markers, function(markerElem) {
              var name = markerElem.getAttribute('name');
              var address = markerElem.getAttribute('address');
              var type = markerElem.getAttribute('type');
              var point = new google.maps.LatLng(
                  parseFloat(markerElem.getAttribute('lat')),
                  parseFloat(markerElem.getAttribute('lng')));

              var infowincontent = document.createElement('div');
              var strong = document.createElement('strong');
              strong.textContent = name
              infowincontent.appendChild(strong);
              infowincontent.appendChild(document.createElement('br'));

              var text = document.createElement('text');
              text.textContent = address
              infowincontent.appendChild(text);
              var icon = customLabel[type] || {};
              var marker = new google.maps.Marker({
                map: map,
                position: point,
                label: icon.label
              });
              marker.addListener('click', function() {
                infoWindow.setContent(infowincontent);
                infoWindow.open(map, marker);
              });

              // Define the LatLng coordinates for the polygon's path.
              var mlat = parseFloat(markerElem.getAttribute('lat'));
              var mlng = parseFloat(markerElem.getAttribute('lng'));
              var edgeSize = 0.005;
              var triangleCoords = [
                {lat: mlat-edgeSize, lng: mlng-edgeSize},
                {lat: mlat-edgeSize, lng: mlng+edgeSize},
                {lat: mlat+edgeSize, lng: mlng+edgeSize},
                {lat: mlat+edgeSize, lng: mlng-edgeSize}
              ];
              // Construct the polygon.
              var bermudaTriangle = new google.maps.Polygon({
                paths: triangleCoords,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35
              });
              bermudaTriangle.setMap(map);

            });
          });
        }
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
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAH0AYzItACHGTE24PNV-88kOfczGdcb90&callback=initMap">
    </script>
  </body>
</html>
