<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="./webStyle.css?modified=20012009" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Traintect</title>
                
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
        <link rel="icon" href="https://image.flaticon.com/icons/png/512/64/64332.png" type="image/x-icon">
    </head>
    
    
    
    <body>
    
    <div class="bgimg w3-display-container w3-animate-opacity w3-text-white">
        <div id="status" class="w3-display-left w3-padding-large"> Status: Offline <br> Train Speed: N/A </div>
        <div class="w3-display-bottomleft w3-padding-large">Traintect All Rights Reserved</div>
    </div>

    
    <!--
    <button onclick="isTrain()"> Demo </button>
    
    <button onclick="changeSpeed()"> Speed </button> 
       
    <p class = "left"> Train speed at LIDAR: <span id = "speed"></span> km/h </p>
    <p> Appoximate Time Until Destination: </p> 
    -->
    
    <div class="w3-display-topleft">
    <img src="./TraintectLogo.png"  style = "margin: 10px 20px" />
    </div>
    
    <script>
        var trainAtIntersection = 0;
        var demo = 0;
        
        // Checking if the train is currently at the intersection or not
        // It starts with 0, will be switched to 1 upon detection of a train 
        // One the train passed said intersection it will be set to 0 again
        function isTrain()
        {
            if (demo == 0)
            {
                window.setTimeout(isTrain, 20000);
            }
            demo = 1;            
            
            if (trainAtIntersection == 1)
            {
                trainAtIntersection = 0;
                // console.log(trainAtIntersection);
            }
            else
            {
                trainAtIntersection = 1;
                // console.log(trainAtIntersection);
            }
        }           
    </script>
    
    
    <div id="map" class="w3-display-bottomright" ></div>
    <script>
        // The initial coordination that the map will centered on center of Saskatoon (saskCoor)
        // The other coordinations are as the names described. Extra coors are used to smoothen the curved path that the train travels.
		var saskCoor = {lat: 52.114558, lng: -106.717831};
        var focusMapCoor = {lat: 52.1321858, lng: -106.7156222};
        var LIDARCoor = {lat: 52.114558, lng: -106.717831};
        var confirmCoor = {lat: 52.129328, lng: -106.717441};
        var extraCoor1 = {lat: 52.133754, lng: -106.717559};
        var extraCoor2 = {lat: 52.137703, lng: -106.713351};
        var destinationCoor = {lat: 52.143756, lng: -106.700490};
        var removalCoor = {lat: 52.148457, lng: -106.690377};
        
		var map;

        // Initializing the map, other functions should be called in here
		function initMap()
        {
            map = new google.maps.Map(document.getElementById('map'), {
    			center: {lat: focusMapCoor.lat, lng: focusMapCoor.lng},
    			zoom: 13   
            })

            initMarkers()
            

            // Calling this every 3.5 second to create following markers
            // Clear this when some kind of flag is set
            addMovingMarker(saskCoor.lat, saskCoor.lng);
    		setInterval ( function(){addMovingMarker(saskCoor.lat, saskCoor.lng);} , 3500 );
        }
    </script>


    <script>
    // Initialization of the static markers on the map
    function initMarkers ()
    {
        marker = new google.maps.Marker({
    				position: {lat: LIDARCoor.lat, lng: LIDARCoor.lng},
                    animation: google.maps.Animation.DROP,
                    icon: 'http://maps.google.com/mapfiles/kml/pushpin/wht-pushpin.png',
    				map: map
    			});
 
 /*
        marker = new google.maps.Marker({
    				position: {lat: confirmCoor.lat, lng: confirmCoor.lng},
                    animation: google.maps.Animation.DROP,
                    icon: 'http://maps.google.com/mapfiles/kml/paddle/blu-circle-lv.png',
    				map: map
    			});
  */
  
        destinationMarker = new google.maps.Marker({
    				position: {lat: destinationCoor.lat, lng: destinationCoor.lng},
                    icon: 'http://maps.google.com/mapfiles/kml/paddle/blu-stars.png',
                    animation: google.maps.Animation.DROP,
    				map: map
    			});
                
        marker = new google.maps.Marker({
    				position: {lat: removalCoor.lat, lng: removalCoor.lng},
                    icon: 'https://cdn0.iconfinder.com/data/icons/fatcow/32/flag_finish.png',
                    animation: google.maps.Animation.DROP,
    				map: map
    			});
    }
    </script>
    
    
	<script>
		var marker = [];
        var markerIndent = 0;

        // Function to initialize marker on the map
		function addMovingMarker(theLat, theLng)
        {
            if (trainAtIntersection == 1)
            {
    			marker[markerIndent] = new google.maps.Marker({
    				position: {lat: theLat, lng: theLng},
                    icon: 'http://maps.google.com/mapfiles/kml/pal4/icon57.png',
    				map: map
    			});

                animateMarker(marker[markerIndent], [
                    // The coordinates of the points that we want markers to go to
                    // Should include the check point, destination and any
                    // coordinations in between them to make smooth movement
                    [confirmCoor.lat, confirmCoor.lng],
                    [extraCoor1.lat, extraCoor1.lng],
                    [extraCoor2.lat, extraCoor2.lng],
                    [destinationCoor.lat, destinationCoor.lng],
                    [removalCoor.lat, removalCoor.lng]
                ], speed);

                markerIndent++;
                // console.log("The markerIndent is " + markerIndent);
            }
		}
	</script>


    <script>
    var startPos = [saskCoor.lat, saskCoor.lng];
    var speed = 1200; // km/h is also the input km_h
    var delay = 100;
    
    
    // Tag the speed so that we can print later on
    document.getElementById("speed").innerHTML = speed;

    // Function that gives smooth animation to the marker
    function animateMarker(marker, coords, km_h)
    {
        var target = 0;
        var km_h = km_h || 50;
        coords.push([startPos[0], startPos[1]]);

        function goToPoint()
        {
            var lat = marker.position.lat();
            var lng = marker.position.lng();
            var step = (km_h * 1000 * delay) / 3600000; // in meters

            var dest = new google.maps.LatLng(
            coords[target][0], coords[target][1]);

            var distance = google.maps.geometry.spherical.computeDistanceBetween(
                                dest, marker.position);; // in meters

            var numStep = distance / step;
            var i = 0;
            var deltaLat = (coords[target][0] - lat) / numStep;
            var deltaLng = (coords[target][1] - lng) / numStep;

            function moveMarker()
            {
                lat += deltaLat;
                lng += deltaLng;
                
                if (lng > destinationCoor.lng)
                {
                    destinationMarker = new google.maps.Marker({
                        position: {lat: destinationCoor.lat, lng: destinationCoor.lng},
                        icon: 'http://maps.google.com/mapfiles/kml/paddle/red-stars.png',
                        // animation: google.maps.Animation.DROP,
                        map: map
                    });
                }
                
                    
                i += step;
   
                if (i < distance)
                {
                    marker.setPosition(new google.maps.LatLng(lat, lng));
                    setTimeout(moveMarker, delay);
                }
                else
                {
                    marker.setPosition(dest);
                    target++;

                    // Terminate the marker
                    // Length - 1 otherwise it will return to original
                    // coordination first and then remove
                    if (target == coords.length - 1)
                    {
                        marker.setMap(null);
                    }

                    setTimeout(goToPoint, delay);
                }
            }
            moveMarker();
        }
        goToPoint();        
    }
    </script>


    <!-- Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->    
    <script type="text/javascript"
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCuNlDVXsbLPt0WtftmzE40ul-EZd9i0qA&callback=initMap&libraries=geometry"
            async defer>
    </script> 
    
  </body>
</html>
