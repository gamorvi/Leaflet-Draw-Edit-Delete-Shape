<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function showMap(){
if(isset($_POST['transform'])){
    $arr = explode( 'geo', str_replace("LatLng", "", str_replace(")", "]", str_replace( "(", "[",$_POST['val'] ) ) ) );
    unset($arr[0]);
    $arrNew = array_values($arr);
    $shapes = array();
    $i = 1;
    if(sizeof($arrNew)>0){
    foreach($arrNew as $row){
        $last = substr($row, -1);
        if($last == ','){
            $row = substr($row, 0, -1);
        } 
        //get coordinates
        $geo = explode("=", $row);
        //get where to set the centre of the map to
        if($i == 1){
            $map = explode(",", str_replace("]", "", str_replace("[", "", $geo[1])));
            $mapPoint = "[".$map[0].",".$map[1]."]";
        }
        //loop through returned records and draw points
        switch($geo[0]){
            case 'POINT':
                //plot point
                $point = 'var marker = L.marker('.$geo[1].',
                        {title: "Hover Text"     
                            /*,opacity: 0.5 */
                            }            
                        )
                        .addTo(drawnItems)
                        .bindPopup("<b>Some Point</b><br>I am lost.")
                        .openPopup(); ';
                array_push($shapes, $point);
            break;
            case 'CIRCLE':
                //plot point
                $point = 'var circle = L.circle('.$geo[1].', '.round($geo[2]).', {
                            color: "red",
                            fillColor: "#f03",
                            /*fillOpacity: 0.5*/
                            }).addTo(drawnItems); ';
                array_push($shapes, $point);
            break;
            case 'POLYGON':
                //plot points
                $point = 'var polyline = L.polyline([
                                '.$geo[1].'
                                ],
                                {   color: "red",
                                    weight: 4,
                                    /*opacity: .7,
                                    dashArray: "20,15",*/
                                    lineJoin: "round"
                                }).addTo(drawnItems); ';
                array_push($shapes, $point);
            break;
            default: echo 'I do not recognise type';
        }
        $i++;
    }
    return array(
        'mapPoint' => $mapPoint,
        'shapes' => $shapes
    );
    } else {
        echo 'No points received';
    }
  }
  
}
$arr = showMap(); 
?>  
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
        <div id="val"></div>
        <script src="dist/leaflet.js" type="text/javascript"></script>
        <script src="dist/leaflet.draw.js" type="text/javascript"></script>
        <script src="http://k4r573n.github.io/leaflet-control-osm-geocoder/Control.OSMGeocoder.js"></script>
        <script src="http://code.jquery.com/jquery-2.1.0.min.js" type="text/javascript"></script>
        
    <script>
        
        
        var map = L.map('map').setView(<?php echo $arr['mapPoint']?>, 14);
        mapLink = 
            '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
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
        //plotting received points
        <?php 
        foreach($arr['shapes'] as $shape){
            echo $shape;
        }
        ?>
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
            //picking coordinates after delete if any
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
                shapes.push('geoCIRCLE='+[layer.getLatLng()]+'='+layer.getRadius());
            }
            //Grab marker or point cordinates
            if (layer instanceof L.Marker) {
                shapes.push('geoPOINT='+[layer.getLatLng()]);
            }

        });

        return shapes;
    };






        </script>
</body>
</html>