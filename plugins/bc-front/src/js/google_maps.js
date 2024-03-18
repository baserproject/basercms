/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

const $script = $("#BsGoogleMapsScript");
const latitude = $script.attr('data-latitude');
const longitude = $script.attr('data-longitude');
const address = $script.attr('data-address');
const zoom = Number($script.attr('data-zoom'));
const mapId = $script.attr('data-mapId');
const title = $script.attr('data-title');
const markerText = $script.attr('data-markerText');

var geo = new google.maps.Geocoder();
var lat = latitude;
var lng = longitude;
if (!lat || !lng) {
    geo.geocode({address: address}, function (results, status) {
        if (status === 'OK') {
            lat = results[0].geometry.location.lat();
            lng = results[0].geometry.location.lng();
            loadMap(lat, lng);
        }
    });
} else {
    loadMap(lat, lng)
}

function loadMap(lat, lng) {
    var latlng = new google.maps.LatLng(lat, lng);
    var options = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        navigationControl: true,
        mapTypeControl: true,
        scaleControl: true,
        scrollwheel: false,
    };
    var map = new google.maps.Map(document.getElementById(mapId), options);
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: title
    });
    if (markerText) {
        var infowindow = new google.maps.InfoWindow({
            content: markerText
        });
        infowindow.open(map, marker);
        google.maps.event.addListener(marker, 'click', function () {
            infowindow.open(map, marker);
        });
    }
}
