<?php
	// GoogleMaps APIの取得
use BcBurgerEditor\Lib\BurgerEditorUtil;

$googleMapsApiKey = BurgerEditorUtil::getGoogleMapApiKey();
?>
<div class="bgt-google-maps" data-lat="35.681382" data-lng="139.766084" data-zoom="16" data-bge="lat:data-lat, lng:data-lng, zoom:data-zoom">
	<img src="//maps.google.com/maps/api/staticmap?center=35.681382,139.766084&amp;zoom=16&amp;size=640x400&amp;&amp;markers=color:red|color:red|35.681382,139.766084<?php if ($googleMapsApiKey) { echo '&amp;key=' . $googleMapsApiKey; } ?>" alt="map" />
</div>
<a class="bgt-google-maps-link" href="//maps.apple.com/?q=35.681382,139.766084" data-bge="url:href" target="_blank" rel="noopener noreferrer"><span>アプリで開く</span></a>
