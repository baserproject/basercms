(()=>{
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
var o=$("#BsGoogleMapsScript"),a=o.attr("data-latitude"),t=o.attr("data-longitude"),e=o.attr("data-address"),n=Number(o.attr("data-zoom")),r=o.attr("data-mapId"),l=o.attr("data-title"),g=o.attr("data-markerText");console.log(n);var d=new google.maps.Geocoder,p=a,m=t;function i(o,a){var t=new google.maps.LatLng(o,a),e={zoom:n,center:t,mapTypeId:google.maps.MapTypeId.ROADMAP,navigationControl:!0,mapTypeControl:!0,scaleControl:!0,scrollwheel:!1},d=new google.maps.Map(document.getElementById(r),e),p=new google.maps.Marker({position:t,map:d,title:l});if(g){var m=new google.maps.InfoWindow({content:g});m.open(d,p),google.maps.event.addListener(p,"click",(function(){m.open(d,p)}))}}p&&m?i(p,m):d.geocode({address:e},(function(o,a){"OK"===a&&(p=o[0].geometry.location.lat(),m=o[0].geometry.location.lng(),i(p,m))}))})();
//# sourceMappingURL=google_maps.bundle.js.map