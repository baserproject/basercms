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
var t=$("#BsGoogleMapsScript"),a=t.attr("data-latitude"),e=t.attr("data-longitude"),o=t.attr("data-address"),n=Number(t.attr("data-zoom")),r=t.attr("data-mapId"),l=t.attr("data-title"),d=t.attr("data-markerText"),g=new google.maps.Geocoder,p=a,m=e;function i(t,a){var e=new google.maps.LatLng(t,a),o={zoom:n,center:e,mapTypeId:google.maps.MapTypeId.ROADMAP,navigationControl:!0,mapTypeControl:!0,scaleControl:!0,scrollwheel:!1},g=new google.maps.Map(document.getElementById(r),o),p=new google.maps.Marker({position:e,map:g,title:l});if(d){var m=new google.maps.InfoWindow({content:d});m.open(g,p),google.maps.event.addListener(p,"click",(function(){m.open(g,p)}))}}p&&m?i(p,m):g.geocode({address:o},(function(t,a){"OK"===a&&(p=t[0].geometry.location.lat(),m=t[0].geometry.location.lng(),i(p,m))}))})();
//# sourceMappingURL=google_maps.bundle.js.map