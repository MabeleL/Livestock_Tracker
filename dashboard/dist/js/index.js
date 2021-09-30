// Initialize and add the map
function initMap() {
  // The location of Uluru
  const ndenderu = {lat:-1.192082, lng:36.743105};
  // The map, centered at Uluru
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 8,
    center: ndenderu,
  });
  // The marker, positioned at ndenderu
const marker = new google.maps.Marker({
    position: ndenderu,
    map: map,
  });


var infoWindow = new google.maps.infoWindow({
  content: '<h3>Ndenderu</h3>'

});

marker.addListener('click', function(){
  infoWindow.open(map, marker);
});

}

/*const image =
    "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";
  const beachMarker = new google.maps.Marker({
    position: ndenderu,
    map: map,
    icon: image,
  });

}
*/
