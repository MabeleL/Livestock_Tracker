

<head>
<!-- daterange picker -->
<link href="<?php echo $base; ?>css/daterangepicker-bs3.css"
	rel="stylesheet" type="text/css" />
<link href="<?php echo $base?>css/polygon_style.css" rel="stylesheet">

<style>
#legend {
	background: white;
	padding: 10px;
}
</style>

</head>
<script type="text/javascript"
	src="http://maps.google.com/maps/api/js?key=AIzaSyDP63aXl_F57f5AsAyx7Arh_IeS9Fzz62A&sensor=false&libraries=geometry"></script>
<script type="text/javascript" src="<?php echo $base?>js/polygon.min.js"></script>
<!-- date-range-picker -->
<script src="<?php echo $base; ?>js/daterangepicker.js" type="text/javascript"></script>
<!--script to display labels for the polygons on the map-->
<script src="https://cdn.rawgit.com/googlemaps/js-map-label/gh-pages/src/maplabel.js"></script>

<script type="text/javascript">
  var past = new Date();
  var today = new Date();
  past.setDate(today.getDate()-5);
  // Global vars for the date range values
  var rangeStartDate = null;
  var rangeEndDate = null;
  var id = null;

  var incidents;
  var markersArray = [];
  var tracksArray = [];
  var displayConflict = false;

  //Global vars for the google map
  var map = null;
  var obj = null;
  var showRecentIncidents = true;

  $(function(){

      //Date range picker
      $('#period').daterangepicker(
      {
          format: 'YYYY-MM-DD'
      },
      // Callback
      function (start, end) {
          rangeStartDate = start.format('YYYY-MM-DD');
          rangeEndDate = end.format('YYYY-MM-DD');
      });

      var myLatlng = new google.maps.LatLng(-2.582722,38.711355);
      var mapOptions = {
        zoom: 10
      }

      map = new google.maps.Map(document.getElementById('map-dashboard'), mapOptions);

        // Try W3C Geolocation (Preferred)
      if(navigator.geolocation) {
        browserSupportFlag = true;
        navigator.geolocation.getCurrentPosition(function(position) {
        initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
        map.setCenter(initialLocation);
      }, function() {
        handleNoGeolocation(browserSupportFlag);
        });
      }
      // Browser doesn't support Geolocation
      else {
       browserSupportFlag = false;
       handleNoGeolocation(browserSupportFlag);
      }

      function handleNoGeolocation(errorFlag) {
      if (errorFlag == true) {
        alert("Geolocation service failed.");

      } else {
        alert("Your browser doesn't support geolocation. We've placed you in Tsavo.");

      }
        map.setCenter(myLatlng);
      }


      //code to create legend for various icons used in the map
      map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(document.getElementById('legend'));
      var legend = document.getElementById('legend');

      var image_url='images/map_icons/';
      var div = document.createElement('div');
      div.innerHTML = '<img src="' + image_url +'poaching.png"> Poaching';
      legend.appendChild(div);

       var div = document.createElement('div');
      div.innerHTML = '<img src="' + image_url +'hwc.png"> Human Wildlife Conflict';
      legend.appendChild(div);

       var div = document.createElement('div');
      div.innerHTML = '<img src="' + image_url +'boma.png"> HWC livestock predation-Inside Kraal';
      legend.appendChild(div);


      var div = document.createElement('div');
      div.innerHTML = '<img src="' + image_url +'pasture.png"> HWC livestock predation-Outside Kraal';
      legend.appendChild(div);


       var div = document.createElement('div');
      div.innerHTML = '<img src="' +  image_url +'mortality.png"> Animal Mortality';
      legend.appendChild(div);

       var div = document.createElement('div');
      div.innerHTML = '<img src="' +  image_url +'iha.png"> Illegal Human Activities';
      legend.appendChild(div);

       var div = document.createElement('div');
      div.innerHTML = '<img src="' +  image_url +'community_service.png"> Community Service';
      legend.appendChild(div);

       var div = document.createElement('div');
      div.innerHTML = '<img src="' +  image_url +'wildlife_sighting.png"> Wildlife Sighting';
      legend.appendChild(div);


      var link = "<?php echo $base; ?>";
      var incidentType = "<?php echo $type; ?>";

      //get all the incidents
      var incidents = '<?php echo $incident_data; ?>';
      obj = jQuery.parseJSON(incidents);
      $.each(obj, function(key,value){

        //get the image corresponding to a particular category
        var category=value.category;
        var image = 'images/map_icons/'+category+'.png';

        var name = value.table_name;
        name = name.replace("_"," ");

        selectedSpecies = "all";

        //check which incident type should be displayed on the map
        if(incidentType == value.category || incidentType == 'all'){
          create_markers(value.table_data,name,image,category,selectedSpecies)
        }

      });



      function create_patrol_tracks(progress,patrol_date,patrol_unit,patrol_method){


        var patrolProgressCoordinates = [];
        $.each(progress, function(key,value){
          patrolProgressCoordinates.push(new google.maps.LatLng(value.latitude, value.longitude));
        });

        var patrolPath = new google.maps.Polyline({
        path: patrolProgressCoordinates,
        geodesic: true,
        strokeColor: '#191970',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        clickable:true
        });

        tracksArray.push(patrolPath);


        var  trackinfowindow = new google.maps.InfoWindow({
          content: patrol_method+" patrol on "+patrol_date+" by "+patrol_unit
        });

        google.maps.event.addListener(patrolPath, 'click', function() {
          trackinfowindow.setPosition(patrolProgressCoordinates[0]);
          trackinfowindow.open(map);
        });

        patrolPath.setMap(map);
      }

      //function to create markers on the map
      function create_markers(data,name,image,category,selectedSpecies){

        obj = data;
        $.each(obj, function(key,value){

        var marker_content;
          //add species information if the incident is part of human wildlife conflict
          if(category == 'hwc'){
           marker_content = name+" caused by "+value.species_name+" reported by "+value.firstName+" "+value.lastName+" on "+value.created
           if(name=='livestock predation'){
             if(value.predationArea=='Inside Kraal'){
              image = 'images/map_icons/boma.png';
             }else{
              image = 'images/map_icons/pasture.png';
             }
           }
          }else if(category == 'wildlife_sighting'){

            if((value.species_name != selectedSpecies) && (selectedSpecies != 'all')){
              return;
            }
              marker_content = value.species_name+"  "+name+" reported by "+value.firstName+" "+value.lastName+" on "+value.created

          }else{
            marker_content = name+" reported by "+value.firstName+" "+value.lastName+" on "+value.created
          }


          var infowindow = new google.maps.InfoWindow({

              content: marker_content

          });

          var myLatlng = new google.maps.LatLng(value.latitude,value.longitude);
          var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: name,
            icon: image
          });
          markersArray.push(marker);
          google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map,marker);
          });

        });

      }

      function displayPatrolTracks(patrolTracks){
        clearPatrolTracks();


        obj = jQuery.parseJSON(patrolTracks);
        $.each(obj, function(key,value){

          create_patrol_tracks(value.locations,value.date,value.patrol_unit,value.patrol_method)


        });

      }

      //function to clear all existing patrol tracks on the map
      function clearPatrolTracks() {
        for (var i = 0; i < tracksArray.length; i++ ) {
          tracksArray[i].setMap(null);
        }
        tracksArray.length = 0;
      }


      //function to clear all existing markers on the map
      function clearOverlays() {
        for (var i = 0; i < markersArray.length; i++ ) {
          markersArray[i].setMap(null);
        }
        markersArray.length = 0;
      }

      function reload_incidents(incidents,incidentType,selectedSpecies){
         var link = "<?php echo $base; ?>";

         //clear all the current markers
         clearOverlays();

         //get all the incidents
         obj = JSON.parse(incidents);
         $.each(obj, function(key,value){

           //get the image corresponding to a particular category
           var category=value.category;
           var image = 'images/map_icons/'+category+'.png';

           var name = value.table_name;
           name = name.replace("_"," ");

           //check which incident type should be displayed on the map
           if(incidentType == value.category || incidentType == 'all'){
             create_markers(value.table_data,name,image,category,selectedSpecies)
           }

         });
         google.maps.event.trigger(map, 'resize');
      }

      //function to load the map with boma vs pasture human wildlife conflict incidents
      function load_conflict_incidents(incidents){
         var link = "<?php echo $base; ?>";
         var incidentType='hwc';
         //clear all the current markers
         clearOverlays();

         //get all the incidents
         obj = JSON.parse(incidents);
         $.each(obj, function(key,value){

           //get the image corresponding to a particular category or incidents e.g poaching etc
           var category=value.category;
           var image = 'images/map_icons/'+category+'.png';

           var name = value.table_name;
           name = name.replace("_"," ");

           //check which incident type should be displayed on the map
           if(incidentType == value.category ){
             create_conflict_markers(value.table_data,name)
           }

         });
         google.maps.event.trigger(map, 'resize');
      }

      //function to create boma vs pasture conflict incidents
      function create_conflict_markers(data,name){
       if(name=='livestock predation'){
        obj = data;
        $.each(obj, function(key,value){

        var marker_content;
        marker_content = name+" caused by "+value.name+" reported by "+value.firstName+" "+value.lastName+" on "+value.created

        var infowindow = new google.maps.InfoWindow({

            content: marker_content

        });

        var myLatlng = new google.maps.LatLng(value.latitude,value.longitude);
        var marker = new google.maps.Marker({
          position: myLatlng,
          map: map,
          title: name,
          icon: image
        });

        markersArray.push(marker);
        google.maps.event.addListener(marker, 'click', function() {
          infowindow.open(map,marker);
        });

        });
      }

      }

      // On click filter
      $("#filter").click(function (e) {
            e.preventDefault();


          if(rangeStartDate == null){

              const date = new Date();

              const month = date.getMonth()+1;  
              const day = date.getDate();
              const year = date.getFullYear();

              if(month <10 || day <10){ 

              if(day < 10 && month < 10){
              const stringDate = year+"-"+"0"+month+"-"+"0"+day;
              const newMonth = month - 6;
              const dateLessSixMonths = year+"-"+"0"+newMonth+"-"+"0"+day;

              rangeStartDate = dateLessSixMonths; 
              rangeEndDate = stringDate;

              }
              else if(day < 10){
              const stringDate = year+"-"+month+"-"+"0"+day;
              const newMonth = month - 6;
              const dateLessSixMonths = year+"-"+"0"+newMonth+"-"+"0"+day;

              rangeStartDate = dateLessSixMonths; 
              rangeEndDate = stringDate;

              }

              else if(month <10){
              const stringDate = year+"-"+"0"+month+"-"+day;
              const newMonth = month - 6;
              const dateLessSixMonths = year+"-"+"0"+newMonth+"-"+"0"+day;

              rangeStartDate = dateLessSixMonths; 
              rangeEndDate = stringDate;

              }


              }
              else{
              //const stringDate = day+"-"+month+"-"+year;
              const stringDate = year+"-"+month+"-"+day;
              const newMonth = month - 6;
              const dateLessSixMonths = year+"-"+"0"+newMonth+"-"+"0"+day;
              rangeStartDate = dateLessSixMonths; 
              rangeEndDate = stringDate;

              }

          }




            showRecentIncidents=false;

            var form = $("#filter-form");

            var link = "<?php echo $base; ?>";
            // Get status
            var selected_incident = $("#incident").val();

            var selected_species = $("#species").val();

            var displayTracks=false;

            var selected=0;
            if( $("#tracks").is(":checked")){
              selected = 1;
              displayTracks=true;
            }else{
              clearPatrolTracks();
            }

            if( $("#conflict").is(":checked")){
              displayConflict=true;
            }else{
              displayConflict=false;
            }


           $.post(link+"dashboard/filter_data",{startDate:rangeStartDate, endDate:rangeEndDate})
           .done(function (data){

            reload_incidents(data,selected_incident,selected_species);

           });

           if(displayTracks){
           $.post(link+"dashboard/filter_patrol_tracks",{startDate:rangeStartDate, endDate:rangeEndDate})
           .done(function (patrols){

            displayPatrolTracks(patrols);

           });

           }
        });

        $("#export").click(function (e) {
            e.preventDefault();
            var startDate = null;
            var endDate = null;

            if(!jQuery.isEmptyObject(obj)){

              var link = "<?php echo $base; ?>";
              // Get status
              var selected_incident = $("#incident").val();
              if(showRecentIncidents){
                startDate =  past.toISOString().substring(0, 10);
                endDate = today.toISOString().substring(0, 10);
              }else{
                startDate = rangeStartDate;
                endDate = rangeEndDate;
              }

              window.location.assign(link+"dashboard/generate_kml_output/"+startDate+"/"+endDate+"/"+selected_incident);
              //display loading gif
              $("#exportProgress").show();
              $.post(link+"dashboard/generate_kml_output",{startDate:startDate, endDate:endDate, incidentType:selected_incident})
                .done(function (data){
                  //hide loading gif
                  $("#exportProgress").hide();
              }).error(function(){
                 alert("Error exporting KML file.");
                 $("#exportProgress").hide();
              });

            }else{
              alert("Please select incidents to export to KML using the date and incident filters");
            }
         });

        //code to hide/show the species selection dropdown
        $('#species_div').hide();
        $("#incident").change(function () {

            var check = "wildlife sighting";
            if ($( "#incident  option:selected" ).text().toLowerCase()===check) {

                $('#species_div').show();
            }
            else {

                $('#species_div').hide();
            }
        });

        $("#print_map").click(function (e) {

          const $body = $('body');
          const $mapContainer = $('#map-dashboard');
          const $mapContainerParent = $mapContainer.parent();
          const $printContainer = $('<div style="position:relative;">');

          $printContainer
            .height($mapContainer.height())
            .append($mapContainer)
            .prependTo($body);

          const $content = $body
            .children()
            .not($printContainer)
            .not('script')
            .detach();

          const $patchedStyle = $('<style media="print">')
            .text(`
              img { max-width: none !important; }
              a[href]:after { content: ""; }
            `)
            .appendTo('head');

          window.print();

          $body.prepend($content);
          $mapContainerParent.prepend($mapContainer);

          $printContainer.remove();
          $patchedStyle.remove();

        });

  });

  //  google.maps.event.addDomListener(window, 'load', initialize);
  </script>

<!--javascript to display the various conservancies on the map-->
<script type="text/javascript" src="<?php echo $base?>js/conservation_areas.js"></script>

<div class="row">
	<div class="col-sm-3">

		<!--<label>Filter by Date range:</label>-->
		<div class="input-group">
			<div class="input-group-addon">
				<i class="fa fa-calendar"></i>
			</div>
			<input type="text" class="form-control pull-right" id="period"
				placeholder="Select date range" />
		</div>
		<!-- /.input group -->


	</div>
	<div class="col-sm-2">

		<div class="form-group">
			<!--<label>Filter by Incident:</label>-->
			<select class="form-control" id="incident">
				<option value="all">All incidents</option>
				<option value="poaching">Poaching</option>
				<option value="mortality">Animal mortality</option>
				<option value="hwc">HWC</option>
				<option value="iha">Illegal human activities</option>
				<option value="community_service">Community service</option>
        <option value="wildlife_sighting">Wildlife sighting</option>
			</select>
		</div>

	</div>
  <div class="col-sm-2" id="species_div">

    <div class="form-group">
      <select class="form-control" id="species">
        <option value="all">All</option>
        <option value="Lion">Lion</option>
        <option value="Elephant">Elephant</option>
        <option value="Cheetah">Cheetah</option>
        <option value="Leopard">Leopard</option>
      </select>
    </div>

  </div>
	<div class="col-sm-1">
		<input type="checkbox" id="tracks" value="tracks">Patrol tracks<br>
	</div>
	<div class="col-sm-1">
		<input type="checkbox" id="conflict" value="conflict">Pasture vs Boma<br>
	</div>
	<div class="col-sm-1">
		<button class="btn btn-success" id="filter">Filter</button>
	</div>
  <div class="col-sm-2">
    <img src="<?php echo $base; ?>images/ajax_loader.gif" style="display: none;" id="exportProgress" />
    <button class="btn btn-primary" id="export">KML</button>
    <!--<button class="btn btn-primary" id="print_map">Print</button>-->
  </div>
</div>

<div id="map-dashboard"></div>

<div id="legend">Map Legend</div>