console.log(OC.currentUser);

if (localStorage.getItem('webpushCurrentSubscription')) {
  document.getElementById("webpushTextCurrentSubscription").innerText = '...' + JSON.parse(localStorage.getItem('webpushCurrentSubscription')).endpoint.slice(-6);
}

$('#webpushBtnSubscribe').on( "click", function() {  

  if (window.Notification.permission === "granted") {
      
    // 1. Get the ServiceWorker, which is needed for Push Subscription Functions
    navigator.serviceWorker.getRegistration('/apps/webpush/js/service-worker-webpush.js').then(
      function (registration) {
          // SW Registration ok?        
          if (registration) {
            console.log("SW already loaded:", registration);
            subscribeUserToPush(registration);

          } else {
            console.log("Empty:", "SW not loaded yet");
            navigator.serviceWorker.register('/apps/webpush/js/service-worker-webpush.js').then(function(registration) {
              console.log("SW registered");
              subscribeUserToPush(registration);              
            });            
          }
      }, 
      function (error) {
          console.log("SW Error:", error);
          alert("Feature not supported, perhaps due to private browsing mode.")
      });
  
    } else {
      alert("Please allow Push Notifications first.\n See top right bell 🔔 icon, \n or allow in Settings.");
  }

});

function subscribeUserToPush(registration) {

  // 2. Now try to Subscribe by using the Servers Vapid Public Key
  if (document.getElementById('webpushHiddenVapidApplicationServerPublicKey').innerText !== "") {

    let serverPublicKey = document.getElementById('webpushHiddenVapidApplicationServerPublicKey').innerText; // = VAPIDs applicationServerKey

    const subscribeOptions = {
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(
        serverPublicKey, 
      ),              
    };

    registration.pushManager.subscribe(subscribeOptions).then(function (pushSubscription) {
      console.log('Received PushSubscription: ', JSON.stringify(pushSubscription)        
      );          

      postSubscription(pushSubscription);
      return pushSubscription;
    }, 
    function (error) {
        console.log("Subscription Error:", error);           
        alert(error);

        // Unsubscription is needed when the Server changed its VAPID Public Key
        registration.pushManager.getSubscription().then((subscription) => {
          subscription
            .unsubscribe()
            .then((successful) => {
              alert("You have been unsubscribed, now please try again.");
            })
            .catch((e) => {
              alert("Unsubscribing failed.");
            });
        });
            
    });

  }

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

async function postSubscription($pushSubscription) {

  let $url = OC.getProtocol() + '://' + OC.getHost() + OC.generateUrl("/apps/webpush/subscribe");

  console.log($pushSubscription);

  let data = {
    subscription: JSON.stringify($pushSubscription)
  }
 
  let $that = this;
  $.ajax({
    url: $url,
    type: 'POST',
    data: data,
    cache: false,                
    success: function(data){    
      console.log("success:", data);
      localStorage.setItem('webpushCurrentSubscription', JSON.stringify(data.subscriptionEcho));
      document.getElementById("webpushTextCurrentSubscription").innerText = '...' + JSON.parse(localStorage.getItem('webpushCurrentSubscription')).endpoint.slice(-6);

    },
    error: function(jqXHR, exception) {
      console.log("error:", exception);
      //$that.loading.addAccount = false;
      //Dialog.alert({ title: 'Error', message: $url + JSON.stringify(jqXHR) });
      //this.appVue.smithers.log("Error", [jqXHR, exception]);
    }
  })

}