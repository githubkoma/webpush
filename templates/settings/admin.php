<?php
//script('webpush', 'adminsettings');
//style('webpush', 'style');
?>

<p>WebPush - Proof of Concept</p> 
<p>Admin Settings</p>

<br>

<p>VAPID_subject: <?php p($_['VAPID_subject']) ?></p>
<p>VAPID_publicKey: <?php p($_['VAPID_publicKey']) ?></p>
<p>VAPID_privateKey: -hidden-</p>
<p hidden>VAPID_pem: <?php //p($_['VAPID_pem']) ?></p>
<p hidden>Link: <?php p(\OC::$server->getURLGenerator()->getAbsoluteURL('/apps/webpush/generateVapidkeys')) ?></p>

<br>

<p>CAUTION: Best Case is you only use this button once after Installation,<br> ⚠️ because after using this button again, EVERY User has to re-subscribe!</p>
<a href="<?php p(\OC::$server->getURLGenerator()->getAbsoluteURL('/apps/webpush/generateVapidkeys')) ?>" target="_blank">
    <button id="webpushBtnGenerateVAPID">Generate new Vapid Keys</button>
</a>