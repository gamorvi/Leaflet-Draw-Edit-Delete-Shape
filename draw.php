
<html>
    <head>
        <title>My Map</title>
        <meta charset="utf-8" />
        <style>
            #map { 
                height: 500px; 
                width: 800px; 
            }
        </style>
        <link href="dist/leaflet.draw.css" rel="stylesheet" type="text/css"/>
        <link href="dist/leaflet.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="http://k4r573n.github.io/leaflet-control-osm-geocoder/Control.OSMGeocoder.css"/>
        
    </head>
    <body>
        <div id="map" ></div>
        <div id="val1"></div>
        <form method="post" action="" id="some_form">
            <input type="hidden" name="val" id="val" />
            <input type="submit" name="transform" value="Transform" style="margin: 40px"/>
        </form>
        <script src="dist/leaflet.js" type="text/javascript"></script>
        <script src="dist/leaflet.draw.js" type="text/javascript"></script>
        <script src="http://k4r573n.github.io/leaflet-control-osm-geocoder/Control.OSMGeocoder.js"></script>
        <script src="http://code.jquery.com/jquery-2.1.0.min.js" type="text/javascript"></script>
        
    <script>
        
        
        var map = L.map('map').setView([-41.2858, 174.78682], 14);
        mapLink = 
            '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
			maxZoom: 22,
			id: 'examples.map-i875mjb7'
        }).addTo(map);
        
        //adding search
        var osmGeocoder = new L.Control.OSMGeocoder({
            collapsed: true,
            //position: 'bottomright',
            text: 'Search'
        });
        map.addControl(osmGeocoder);
        
        //adding drawing elements
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);
        
        //configuring what shapes users can draw
        var drawControl = new L.Control.Draw({
			position: 'topright',
			draw: {
				polyline: false ,
				polygon: {
					allowIntersection: false,
					showArea: true,
					drawError: {
						color: '#b00b00',
						timeout: 1000
					},
					shapeOptions: {
						color: '#000000'
					}
				},
				circle: {
					shapeOptions: {
						color: '#662d91'
					}
				},
				marker: true
			},
			edit: {
				featureGroup: drawnItems
				//,remove: false
			}
		});
        map.addControl(drawControl);
        
        //creating a new point event
        map.on('draw:created', function (e) {
            var type = e.layerType,
                layer = e.layer;
            drawnItems.addLayer(layer);

        //grabbing the shape drawn
        var shapes = getShapes(drawnItems);
        $('#val1').html(shapes);
        $('#val').val(shapes);
        });
        //edit point event
        map.on('draw:edited', function (e) {
        var layers = e.layers;
            layers.eachLayer(function (layer) {
            //pick new coordinate after edit
            var shapes = getShapes(drawnItems);
            $('#val1').html(shapes);
            $('#val').html(shapes);
            });
        });
        //delete event
        map.on('draw:deleted', function () {
            var shapes = getShapes(drawnItems);
            $('#val1').html(shapes);
            $('#val').html(shapes);
        });
 
    var getShapes = function(drawnItems) {

    var shapes = [];

    drawnItems.eachLayer(function(layer) {

            // Note: Rectangle extends Polygon. Polygon extends Polyline.
            // Therefore, all of them are instances of Polyline
            if (layer instanceof L.Polyline) {
                shapes.push('geoPOLYGON='+layer.getLatLngs());
            }
            //Grab Circle coordinates and radius
            if (layer instanceof L.Circle) {
                shapes.push('geoCIRCLE='+[layer.getLatLng()]+'radius='+layer.getRadius());
            }
            //Grab marker or point cordinates
            if (layer instanceof L.Marker) {
                shapes.push('geoPOINT='+[layer.getLatLng()]);
            }

        });

        return shapes;
    };
        </script>
<?php


if(isset($_POST['transform'])){
    $arr = explode( 'geo', str_replace("LatLng", "", str_replace(")", "]", str_replace( "(", "[",$_POST['val'] ) ) ) );
    echo '<pre>';
    print_r($arr);
    echo '<pre>';
}


?>  
    </body>
</html>

