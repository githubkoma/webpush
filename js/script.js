alert(window.Notification.permission);
console.log(OC.currentUser);

if (window.Notification.permission === "granted") {
    
    navigator.serviceWorker.getRegistration('/apps/webpush/js/service-worker-webpush.js').then(
      function (registration) {
          // SW Registration ok?        
          if (registration) {
            console.log("SW already loaded:", registration);
            subscribeUserToPush(registration);

          } else {
            console.log("Empty:", "SW not loaded yet");
            navigator.serviceWorker.register('/apps/webpush/js/service-worker-webpush.js').then(function(registration) {
              subscribeUserToPush(registration);
              console.log("SW registered")
            });            
          }
      }, 
      function (error) {
          console.log("SW Error:", error);
      });
}

function subscribeUserToPush(registration) {

  // https://github.com/web-push-libs/
  //   create VAPID: https://github.com/web-push-libs/vapid
  //   send via CLI: https://github.com/web-push-libs/pywebpush
  let serverPublicKey = ""; // = VAPIDs applicationServerKey

  const subscribeOptions = {
    userVisibleOnly: true,
    applicationServerKey: urlBase64ToUint8Array(
      serverPublicKey, 
    ),              
  };

  registration.pushManager.subscribe(subscribeOptions).then(function (pushSubscription) {
    console.log(
      'Received PushSubscription: ',
      JSON.stringify(pushSubscription),
    );
    alert(JSON.stringify(pushSubscription));
    return pushSubscription;
  });

}

function urlBase64ToUint8Array(base64String) {
  var padding = '='.repeat((4 - base64String.length % 4) % 4);
  var base64 = (base64String + padding)
      .replace(/-/g, '+')
      .replace(/_/g, '/');

  var rawData = window.atob(base64);
  var outputArray = new Uint8Array(rawData.length);

  for (var i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}