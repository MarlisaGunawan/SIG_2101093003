<?php
 require_once("db.php");
 $conn=new connectToDB;
 $companies = $conn->getCompaniesList();
 $streets = $conn->getStreetsList();
 $areas = $conn->getAreasList(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<base target="_top">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Layers Control Tutorial - Leaflet</title>
	
	<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

	<style>
		html, body {
			height: 100%;
			margin: 0;
		}
		.leaflet-container {
			height: 400px;
			width: 600px;
			max-width: 100%;
			max-height: 100%;
		}
	</style>

	
</head>
<body>

<div id="map" style="width: 600px; height: 400px"></div>

<script>
	const map = L.map('map').setView([-0.9457977488122259, 100.40319317528338], 14);
	var perusahaan = L.layerGroup();
	var jalan = L.layerGroup();
	var area = L.layerGroup();

	const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	});

	var mapboxUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';
 
	var streets = L.tileLayer(mapboxUrl, {
		id: 'mapbox/streets-v11', 
		tileSize: 512, 
		zoomOffset: -1
	});

	var satellite = L.tileLayer(mapboxUrl, {
		id: 'mapbox/streets-v9', 
		tileSize: 512, 
		zoomOffset: -1
	});
	/* var map = L.map('map',{
		center:([-0.9457977488122259, 100.40319317528338], 13),
		zoom: 19,
		layers : [osm,perusahaan]
	}); */

	$( document ).ready(function() {
		addCompanies();   
		addStreets();   
		addAreas();   
  });

	var baseMaps={
		"OpenStreetMap" : osm,
		"MapBoxStreet" : streets,
		"Satellite" : satellite
	}

	var overlays = {
		"Perusahaan" : perusahaan,
		"Jalan" : jalan,
		"Area" : area
	}

	function addCompanies() {
	for(var i=0; i<companies.length; i++) {
		var marker = L.marker( [companies[i]['latitude'], companies[i]['longitude']]).addTo(map);
		marker.bindPopup( "<b>" + companies[i]['company']+"</b><br>Details:" + companies[i]['details'] + "<br />Telephone: " + companies[i]['telephone']);
	}
   }

   function stringToGeoPoints(geo){
	var linesPin = geo.split(",");
		var linesLat = new Array();
		var linesLng = new Array();
	for (i = 0; i < linesPin.length; i++){
		if(i%2){
			linesLng.push(linesPin[i]);
		}else{
			linesLng.push(linesPin[i]);
		}
	}

	var latLngLine = new Array();
		for(i=0;i<linesLng.length;i++){
			latLngLine.push(L.latLng(linesLat[i], linesLng[i]));
		}
		return latLngLine;
   }

   function addAreas() {
   for(var i=0; i < areas.length; i++) {
    var polygon = L.polygon( stringToGeoPoints(areas[i]['geolocations']), { color: 'blue'}).addTo(map);
    polygon.bindPopup( "<b>" + areas[i]['name']);   
   }
  }
  
  function addStreets() {
   for(var i=0; i < streets.length; i++) {
    var polyline = L.polyline( stringToGeoPoints(streets[i]['geolocations']), { color: 'red'}).addTo(map);
    polyline.bindPopup( "<b>" + streets[i]['name']);   
   }
  }

	var companies = JSON.parse('<?php echo json_encode($companies)?>');
	var streets = JSON.parse('<?php echo json_encode($streets)?>');
	var areas = JSON.parse('<?php echo json_encode($areas)?>');

	var layerControl = L.control.layers(baseMaps,overlays).addTo(map);
</script>
</body>
</html>
