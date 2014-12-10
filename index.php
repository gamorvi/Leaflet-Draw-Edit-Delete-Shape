<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<html>
    <head>
        <title>My Map</title>
        <meta charset="utf-8" />
        <style>
            #map { 
                height: 180px; 
                width: 600px; 
                height: 300px
            }
        </style>
        <link href="vendor/leaflet.css" rel="stylesheet" type="text/css"/>
        
        
    </head>
    <body>
        <div id="map" ></div>
        <div id="val"></div>
        <script src="vendor/leaflet.js" type="text/javascript"></script>
        <script
        src="http://leaflet.github.io/Leaflet.draw/leaflet.draw.js">
    </script>
        <script>
            var map = L.map('map').setView([51.505, -0.09], 13);
            
            L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
			maxZoom: 18,
			id: 'examples.map-i875mjb7'
		}).addTo(map);

            var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems
            }
        });
        map.addControl(drawControl);
        
        map.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer;
            drawnItems.addLayer(layer);
        });
        
        
            var popup = L.popup();

            function onMapClick(e) 
            {
		popup
                    .setLatLng(e.latlng)
                    .setContent( e.latlng.toString() )
                    .openOn(map);
            }
            map.on('click', onMapClick);
             
            var marker = L.marker([51.5, -0.09],
            {draggable: true}).addTo(map);
            
            function onClicker(e){
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;
                if (typeof marker != 'undefined') {
                    map.removeLayer(marker);
                    marker = L.marker([lat, lng]).addTo(map);
                } else {
                    marker = L.marker([lat, lng]).addTo(map);
                }
            }

        </script>
         
    </body>
</html>

