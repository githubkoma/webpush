<?php
script('webpush', 'personalsettings');
//style('webpush', 'style');
?>

<p>WebPush - Proof of Concept</p> 
<p>Personal Settings</p>

<p id="webpushHiddenVapidApplicationServerPublicKey" hidden><?php p($_['VAPID_publicKey']) ?></p>

<button id="webpushBtnSubscribe">Subscribe to WebPush</button>

<p>My WebPush Subscription:
    <span id="webpushTextCurrentSubscription">-empty-</span>
</p>