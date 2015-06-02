function initializeMap(id, x, y){
        var map_canvas = document.getElementById(id);
        var map_options = {
            center: new google.maps.LatLng(x, y),
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(map_canvas, map_options);
        var myLatLng = new google.maps.LatLng(x, y);
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map
        });
        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            $('#locationX').val(event.latLng.lat());
            $('#locationY').val(event.latLng.lng());
        });
}