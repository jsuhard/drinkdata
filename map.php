<?php
include 'includes/common.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>DataDrink</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<style>
html, body { height: 100%; }
#map-canvas {
	height: 100%;
	width: 100%;
}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDblGl4Z5V4DWId3m44DV0qICON6RHDZ74&libraries=visualization,places"></script>
<script>
var heatMapData = [
<?php
	$loc_center = $user->getPositionsCenter();
	foreach($user->getPositions() as $loc) {
?>
  {location: new google.maps.LatLng(<?=$loc['lat']?>,<?=$loc['lng']?>), weight: <?=$loc['weight']?>},
<?php
	}
?>
];

var map;
var infowindow;
var circle;
var service;
var markers = [];
var heatmap;

var types = ['bar', 'cafe', 'night_club', 'restaurant'];

function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(<?=$loc_center['lat']?>, <?=$loc_center['lng']?>),
          zoom: 13
        };
        map = new google.maps.Map(document.getElementById("map-canvas"),
            mapOptions);
	infowindow = new google.maps.InfoWindow();
	circle = new google.maps.Circle();
	service = new google.maps.places.PlacesService(map);

        heatmap = new google.maps.visualization.HeatmapLayer({
          data: heatMapData
        });
	heatmap.set('radius', 30);
	heatmap.set('opacity', 0.8);
	heatmap.set('maxIntensity', 200);
	
/*	
	var transitLayer = new google.maps.TransitLayer();
  	transitLayer.setMap(map);
*/
<?php
	foreach($user->getPlaces() as $loc) {
?>
	createMarker(<?=$loc['lat']?>,<?=$loc['lng']?>,<?=$loc['accuracy']?>,<?=$loc['quantity']?>,"<?=$loc['type']?>","<?=$loc['name']?>");
<?php
	}
?>

	showMarkers();
	showHeatmap();
}

var time = 0;

function get_place_name(position, radius, callback) {
	setTimeout(function(){service.nearbySearch({location: position, radius: radius, types: types}, callback)}, time);
	time += 200;
}

function createMarker(lat, lng, accuracy, quantity, type, name) {
  var position = new google.maps.LatLng(lat, lng);
  var marker = new google.maps.Marker({
    map: map,
    position: position
  });
  markers.push(marker);

  var color = 'FF0000';
  var options = {
      strokeColor: '#'+ color,
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#'+ color,
      fillOpacity: 0.0,
      map: map,
      center: position,
      radius: accuracy
  };
  
  var radius = (accuracy < 10) ? 10 : accuracy;

  function callback(results, status) {
	//console.log(status);
  	if (status == google.maps.places.PlacesServiceStatus.OK) {
    		name = results[0].name;
  	}else if(status == google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT) {
		get_place_name(position, radius, callback);
	}
  }
 
  if(name === null) {
	get_place_name(position, radius, callback);
  }

  google.maps.event.addListener(marker, 'click', function() {
    circle.setOptions(options);
    infowindow.setContent('<div style="height: 40px; width:120px;"><h6>'+ name +'</h6><span class="text-nowrap">'+ quantity +' cl de '+ type +'</span></div>');
    infowindow.open(map, this);
  });

}

// Sets the map on all markers in the array.
function setAllMap(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
  $('#markers').removeClass('active');
  setAllMap(null);
}

// Shows any markers currently in the array.
function showMarkers() {
  $('#markers').addClass('active');
  setAllMap(map);
}

function toggleMarkers() {
	if(markers[0].getMap() == null) {
		showMarkers();
	}else {
		clearMarkers();
	}
}

function clearHeatmap() {
	$('#heatmap').removeClass('active');
	heatmap.setMap(null);
}

function showHeatmap() {
	 $('#heatmap').addClass('active');
	heatmap.setMap(map);
}

function toggleHeatmap() {
	if(heatmap.getMap() == null) {
		showHeatmap();
	}else {
		clearHeatmap();
	}
}

google.maps.event.addDomListener(window, 'load', initialize);

$( document ).ready(function() {
	$('#markers').click(function() {
		toggleMarkers();
	});
	$('#heatmap').click(function() {
		toggleHeatmap();
	});
	
});
</script>
</head>

<body>
<div class="container" style="height: 100%;">

<nav role="navigation" class="navbar navbar-inverse navbar-static-top">
    <!-- container-fluid -->    
    <div class="container-fluid">
        <!-- navbar-header -->
        <div class="navbar-header">
              
              <a href="./" class="navbar-brand">Home</a>
              <ul class="nav navbar-nav nav-pills">
                  <li class="active"><a href="map.php"><span class="glyphicon glyphicon-globe"></span></a></li>
		  <li class="active"><a href="stats.php"><span class="glyphicon glyphicon-stats"></span></a></li>
		  <li class="active"><a href="#"><span class="glyphicon glyphicon-dashboard"></span></a></li>
		  <li class="active pull-right"><a href="phpliteadmin.php"><span class="glyphicon glyphicon-wrench"></span></a></li>
              </ul>
        </div>
        <!-- /navbar-header -->
    </div>
    <!-- /container-fluid -->    
</nav>


<div class="panel panel-default" style="height: 70%;">
  <div class="panel-heading">Carte Google Maps 
	<div class="pull-right">
		<button class="btn btn-default" id="markers"><span class="glyphicon glyphicon-map-marker"></span></button>
		<button class="btn btn-default" id="heatmap"><span class="glyphicon glyphicon-cloud"></span></button>
		<button class="btn btn-default" onclick="$('#map-canvas').height(200 + $('#map-canvas').height());"><span class="glyphicon glyphicon-resize-vertical"></span></button>
	</div>	
  </div>
  <div class="panel-body" id="map-canvas">
  </div>
</div>

</div>
</body>
</html>
