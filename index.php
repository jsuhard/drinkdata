<?php
include 'includes/common.php';

if (!empty($_POST) ) {
	if((empty($_POST['quantity'])) || (empty($_POST['type']))) { 
		$message= '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <strong>Warning!</strong>Tous les champs doivent être remplis!</div>';
	}else {
		$lat = (floatval($_POST['lat']) != 0) ? floatval($_POST['lat']) : null;
		$lng = (floatval($_POST['lng']) != 0) ? floatval($_POST['lng']) : null;
		$accuracy = (intval($_POST['accuracy']) > 0) ? intval($_POST['accuracy']) : null;

		$bdd = sql_connect();

		sql_execute($bdd, 'INSERT INTO data(date, quantity, latitude, longitude, accuracy, type, comment, user_id, gmaps_place_id) VALUES (strftime("%s","now"),:quantity,:lat,:lng,:accuracy,:type,:comment,:user_id, :gmaps_place_id)',
		array(
		  'quantity' => $_POST["quantity"],
		  'type' => $_POST["type"],
		  'lat' => $lat,
		  'lng' => $lng,
		  'accuracy' => $accuracy,
		  'comment' => $_POST['comment'],
		  'user_id' => $_POST['user_id'],
		  'gmaps_place_id' => $_POST['gmaps_place_id'],
		));
 
		  $message = '<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <strong>Cool!</strong> votre verre de ' .$_POST["quantity"]. ' centilitres de ' .$_POST["type"].' a bien été ajouté !</div>';
	}
}
?>
<!DOCTYPE html>
<html manifest="mobile.appcache">
<head>
<meta charset="utf-8">
<title>DataDrink</title>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<link rel="shortcut icon" href="img/beer.svg">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
#map-canvas {
	height: 200px;
	width: 100%;
}
.spin {
	animation:spin 1s linear infinite;
	-webkit-animation:spin 1s linear infinite;
}
@keyframes spin { 100% { transform:rotate(360deg); } }
@-webkit-keyframes spin { 100% { transform:rotate(360deg); } }
.img-beer,.img-shot-20,.img-shot-40,.img-vine {
	background-repeat: no-repeat;
	height: 145px;
	width: 145px;
}
.img-beer {
	background-color: #FBB033;
	background-image: url(img/beer.svg);
}
.img-shot-20,.img-shot-40 {
	background-image: url(img/shot.svg);
	background-position: 50% -20px; 
	font-size: 42pt;
	padding-top: 70px;
	height: 145px;
}
.img-shot-20 {
	background-color: #29ABE2;
}
.img-shot-20::after {
	content: ' +20°';
}
.img-shot-40 {
	background-color: #8CC63F;
}
.img-shot-40::after {
        content: ' +40°';
}
.img-vine {
	background-color: #AB3DA0;
	background-image: url(img/vine.svg);
}
</style>
</head>
<body>
<div class="container">
<?=$message?>


<form class="form-horizontal" role="form" method="post">

<div class="form-group">
<div class="btn-group " data-toggle="buttons">
  <label class="btn btn-default col-xs-6" id="type-vin">
    <input type="radio" name="type" id="option1" value="vin"> <span class="img-vine img-responsive center-block"></span>
  </label>
  <label class="btn btn-default col-xs-6 active" id="type-bière">
    <input type="radio" name="type" id="option1" value="bière" checked="checked"> <span class="img-beer img-responsive center-block"></span>
  </label>
  <label class="btn btn-default col-xs-6" id="type-shot-20">
    <input type="radio" name="type" id="option1" value="shot-20"> <span class="img-shot-20 img-responsive center-block"></span>
  </label>
  <label class="btn btn-default col-xs-6" id="type-shot-40">
    <input type="radio" name="type" id="option1" value="shot-40"> <span class="img-shot-40 img-responsive center-block"></span>
  </label>
</div>
</div>

<!--
<div class="form-group">
    <label for="inputType" class="col-sm-2 control-label">Type</label>
    <div class="col-sm-10">
<div class="btn-group btn-group-justified text-center" data-toggle="buttons">
  <label class="btn btn-default">
    <input type="radio" name="type" id="option1" value="vin"> <img src="img/vin.png" class="img-responsive center-block">
  </label>
  <label class="btn btn-default active">
    <input type="radio" name="type" id="option1" value="bière" checked="checked"> <img id="img-beer" src="img/beer.svg" class="img-responsive center-block">
  </label>
  <label class="btn btn-default">
    <input type="radio" name="type" id="option1" value="+20°"> <img src="img/+20.png" class="img-responsive center-block">
  </label>
  <label class="btn btn-default">
    <input type="radio" name="type" id="option1" value="+40°"> <img src="img/+40.png" class="img-responsive center-block">
  </label>
</div>
    </div>
</div>
-->

<div class="form-group">
    <label for="inputQuantity" class="col-sm-2 control-label">Quantité ( cl )</label>
    <div class="col-sm-10">
<div class="btn-group" id="inputQuantity" data-toggle="buttons">
  <label class="btn btn-default btn-lg" id="pencil-quantity">
    <input type="radio" name="quantity" value="100"> <span class="glyphicon glyphicon-pencil"></span>
  </label>
<!--
  <label class="btn btn-default">
    <input type="number" name="quantity" id="option3" value="100" min="1" step="1"> cl
  </label>
-->
</div>
<!--
<div class="btn-group" data-toggle="buttons">
  <label class="btn btn-default btn-lg active">
    <input type="radio" name="unit" > cl
  </label>
  <label class="btn btn-default btn-lg">
    <input type="radio" name="unit" > oz
  </label>
</div>
-->
   </div>
</div>
	
<input type="hidden" name="lat" id="lat">
<input type="hidden" name="lng" id="lng">
<input type="hidden" name="accuracy" id="accuracy">
<input type="hidden" name="gmaps_place_id">

<div class="form-group">
        <div class="col-xs-10 col-sm-offset-2 col-sm-10 btn-group btn-group-lg">
		<button type="submit" class="btn btn-primary">Ajouter une conso</button>
		<button type="button" onclick="getLocation()" class="btn btn-default" id="geolocation" disabled><span class="glyphicon glyphicon-globe" id="globe"></span></button>
	</div>
	<div class="col-xs-2">
		<button type="button" onclick="getLocationDebug()" class="btn btn-default"><span class="glyphicon glyphicon-random"></span></button>
	</div>
</div>

<div id="error"></div>


<div class="panel panel-default hidden" id="google">
  <div class="panel-heading">Position actuelle</div>
  <div class="panel-body" id="map-canvas">
  </div>
  <p id="lieux_possibles" class="hidden">Lieux possibles:</p>
  <ul class="list-group">
  </ul>
</div>


<div class="form-group">
    <label for="inputComment" class="col-xs-3 col-sm-2 control-label">Lieu</label>
    <div class="col-xs-9 col-sm-10">
        <input name="comment" class="form-control" >
    </div>
</div>


<div class="form-group">
    <label for="inputUser" class="col-xs-3 col-sm-2 control-label">Utilisateur</label>
    <div class="col-xs-9 col-sm-10">
	<select name="user_id" id="inputUser" class="form-control">
<?php
	foreach(sql_select('id, firstname FROM users') as $u) {
?>
 		<option value="<?=$u['id']?>"><?=$u['firstname']?></option>
<?php
	}
?>
	</select>
    </div>
</div>

<div class="panel panel-default hidden" id="debug_coords">
  <div class="panel-heading">Debug Coords</div>
  <div class="panel-body">
  </div>
  <table class="table table-condensed table-bordered text-center">
	<tr><th>Lat</th><th>Lng</th><th>Précision (m)</th></tr>
  </table>
</div>

</form>



<br/>
<a href="map.php">Google Map</a>

</div>

<script src="js/geo.js"></script>
<script>
var x = document.getElementById("error");

function getLocation() {
    if (navigator.geolocation) {
	$('#globe').addClass('spin');
	$("input#accuracy").val(null);
        //navigator.geolocation.getCurrentPosition(showProgress,showError);
	navigator.geolocation.getAccurateCurrentPosition(showPosition,showError,showProgress, {desiredAccuracy: 2/*<?=POSITION_GOOD?>*/});
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function getLocationDebug() {
	$('#globe').addClass('spin');
        $("input#accuracy").val(null);
	$('#google > ul.list-group').children().remove();
	var places = [
<?php
	$places = sql_select('latitude AS lat, longitude AS lng, accuracy, comment AS name
FROM "data" 
WHERE latitude != ""
AND longitude != ""
AND accuracy != 0 
AND comment != ""
AND comment = "Buck Mulligan\'s"
ORDER BY RANDOM()
LIMIT 20');
	foreach($places as $place) {
?>	
		{lat: 47.2157301, lng: -1.5525646, name: "<?=$place['name']?>", accuracy: 5},
<?php
	}
?>
	];
	var place = places[Math.floor(Math.random()*places.length)];
	var lat = place.lat;
	var lng = place.lng;
	var accuracy = place.accuracy;
	//$('input[name="comment"]').val(place.name);
	function getRandomAccuracy() {
	var min = -0.0002;
	var max =  0.0002;
	return Math.random() * (max - min) + min;
	}
	setTimeout(function() {showProgress({coords: {latitude: lat + getRandomAccuracy(), longitude: lng + getRandomAccuracy(), accuracy: accuracy + 40}})}, 1000);
	setTimeout(function() {showProgress({coords: {latitude: lat, longitude: lng, accuracy: accuracy + 20}})}, 5000);
	setTimeout(function() {showPosition({coords: {latitude: lat, longitude: lng, accuracy: accuracy     }})}, 9000);
}

function showPosition(position) {
    $('#globe').removeClass('spin');
    showProgress(position);
}

function showProgress(position) {
    var accuracy = position.coords.accuracy;
    var accuracy_old = $("input#accuracy").val();
    if(accuracy < accuracy_old || accuracy_old == 0 || accuracy_old === undefined) {
    	$("input#lat").val(position.coords.latitude); 
    	$("input#lng").val(position.coords.longitude);
    	$("input#accuracy").val(accuracy);
	updateLocation(position.coords.latitude, position.coords.longitude, accuracy);
    }
}

function showError(error) {
    $('#globe').removeClass('spin');
    switch(error.code) {
        case error.PERMISSION_DENIED:
            x.innerHTML = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            x.innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            x.innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            x.innerHTML = "An unknown error occurred."
            break;
    }
}
</script>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
function setCookie(key, value) {
	var expires = new Date();
        expires.setTime(expires.getTime() + (100 * 24 * 60 * 60 * 1000));
        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function getCookie(key) {
        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
}

$( document ).ready(function() {
	function jq_id( myid ) {
	return "#" + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
	}

	function showQuantities(liste) {
		$('[id^=quantity-]').remove();
		liste.sort(function(a, b) { return b - a; });
		liste.forEach(function(quantity) {
			$('#inputQuantity').prepend('<label class="btn btn-default btn-lg" id="quantity-'+ quantity +'"><input type="radio" name="quantity" value="'+ quantity +'"> '+ quantity +'</label>');
		});
	}
	function selectQuantity(quantity) {
		$(jq_id('quantity-'+ quantity)).click();
	}

	$('#inputUser').change(function() {
		setCookie('user_id', this.value);
	});
	if(getCookie('user_id') > 0) {
		$('#inputUser').val(getCookie('user_id'));
	}
	$('#pencil-quantity').click(function() {
		var number_old = $(this).children().attr('value');
		var number_new = parseFloat(prompt('Quantity ?', number_old));
		$(this).children().attr('value', number_new);
		$(this).children('span').html(' ('+ number_new +')');
	});
	$('#type-vin').click(function() {
		showQuantities([10, 12.5, 25]);
		selectQuantity('10');
	});
	$('#type-bière').click(function() {
                showQuantities([12.5, 25, 33, 50]);
		selectQuantity('25'); 
        });
	$('[id^=type-shot]').click(function() {
                showQuantities([2, 4, 7, 10]);
                selectQuantity('4'); 
        });
	$('#google ul.list-group').on('click', 'li', function() {
		//alert($(this).text());
		$('input[name="comment"]').val($(this).text());
		$('input[name="gmaps_place_id"]').val($(this).attr('id'));
	});
	

	$('#type-<?=$user->getMostType()['type']?>').click();
	selectQuantity(<?=$user->getMostType()['quantity']?>);

});
</script>
<script src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyDblGl4Z5V4DWId3m44DV0qICON6RHDZ74&libraries=places,visualization"></script>
<script>

function initialize() {
	$('#geolocation').prop('disabled', false);
/* 
       var mapOptions = {
          center: new google.maps.LatLng(<?=$loc_center['lat']?>, <?=$loc_center['lng']?>),
          zoom: 13,
	  draggable: false,
	  scrollwheel: false,
	  streetViewControl: false,
        };
        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
*/
new google.maps.LatLng(1.0, 1.0);
}

function loadGoogleMaps() {
	if(google.maps === undefined) {
	  var script = document.createElement('script');
	  script.type = 'text/javascript';
	  script.src = 'https://maps.googleapis.com/maps/api/js?v=3' +
	      '&key=AIzaSyDblGl4Z5V4DWId3m44DV0qICON6RHDZ74&libraries=places,visualization&callback=initialize';
	  document.body.appendChild(script);
	}
}

var map;
var service;
var marker;
var circle;
function updateMap(center) {
	if(map == null) {
		var mapOptions = {
	          center: center,
	          zoom: 18,
	          draggable: false,
	          scrollwheel: false,
	          streetViewControl: false,
		  /*mapTypeId: google.maps.MapTypeId.HYBRID,*/
		  tilt: 0,
		  styles: [
			{
				featureType: "poi",
				elementType: "all",
				stylers: [ { visibility: "off" } ]
			},
	        	{
	            		featureType: "poi.business",
	            		elementType: "all",
	            		stylers: [ { visibility: "on" } ]
	        	},
	    	  ]
		};
		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
		service = new google.maps.places.PlacesService(map);
	}else {
		map.panTo(center);
	}
}

function updateMarker(position, color, radius) {
	updateMap(position);

	var icon = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|'+ color;
	if( marker == null) {
		marker = new google.maps.Marker({
    			map: map,
    			position: position,
    			icon: icon,
  		});
	}else {
		marker.setIcon(icon);
		marker.setPosition(position);
	}
  
  var options = {
      strokeColor: '#'+ color,
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: '#'+ color,
      fillOpacity: 0.15,
      map: map,
      center: position,
      radius: radius
    };

	if(circle == null) {
		circle = new google.maps.Circle(options);
	}else {
		circle.setOptions(options);
	}
}

function updateLocation(lat, lng, accuracy) {
  $('#google').removeClass('hidden');
  $('#debug_coords').removeClass('hidden');
  $('#debug_coords table').append('<tr><td>'+ lat.toFixed(5) +'</td><td>'+ lng.toFixed(5) +'</td><td>'+ Math.round(accuracy) +'</td></tr>');
  var button = $('button[type=submit]');
  if(accuracy <= <?=POSITION_GOOD?>) {
	var color = '00FF00';
	button.removeClass('btn-warning btn-danger').addClass('btn-success');
  }else if(accuracy < <?=POSITION_BAD?>) {
	var intervalle = <?=POSITION_BAD?> - <?=POSITION_GOOD?>;
	var ratio = (accuracy - <?=POSITION_GOOD?>) / intervalle;
	var color_start = '00FF00';
	var color_end = 'FF0000';
	var hex = function (x) {
  		x = x.toString(16);
  		return (x.length == 1) ? '0' + x : x;
	};
	var r = Math.ceil(parseInt(color_end.substring(0, 2), 16) * ratio + parseInt(color_start.substring(0, 2), 16) * (1 - ratio));
	var g = Math.ceil(parseInt(color_end.substring(2, 4), 16) * ratio + parseInt(color_start.substring(2, 4), 16) * (1 - ratio));
	var b = Math.ceil(parseInt(color_end.substring(4, 6), 16) * ratio + parseInt(color_start.substring(4, 6), 16) * (1 - ratio));
	var color = hex(r) + hex(g) + hex(b);
	button.removeClass('btn-danger').addClass('btn-warning');
  }else {
	var color = 'FF0000';
	button.addClass('btn-danger');
  }
  
  if(google.maps === undefined) {
		//loadGoogleMaps();
  }
  
  var position = new google.maps.LatLng(lat, lng);

updateMarker(position, color, accuracy);

	function callback(results, status) {
 		 if (status == google.maps.places.PlacesServiceStatus.OK) {
			$('#lieux_possibles').removeClass('hidden');
			$('#google > ul.list-group').children().remove();
    		 	for (var i = 0; i < results.length; i++) {
      				var place = results[i];
				var open_now = ( place.opening_hours === undefined ) ? true : place.opening_hours.open_now; 
				if(open_now) {
					$('#google > ul.list-group').append('<li class="list-group-item" id="'+ place.place_id +'">'+ place.name +'</li>');
				}
    			}
  		}
	}

//service.nearbySearch({location: position, rankBy: google.maps.places.RankBy.DISTANCE, types: ['bar', 'cafe', 'night_club', 'restaurant', 'establishment']}, callback);
service.nearbySearch({location: position, radius: accuracy + 45, types: ['bar', 'cafe', 'night_club', 'restaurant', 'establishment']}, callback);
}

google.maps.event.addDomListener(window, 'load', initialize);

</script>
</body>
</html>
