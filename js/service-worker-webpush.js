// Basic SW from:
// https://www.makeuseof.com/javascript-service-workers/
self.addEventListener('install', (event) => {
    event.waitUntil(new Promise((resolve, reject) => {
            console.log("doing setup stuff")
            resolve()
        }))
        console.log("Service worker finished installing")
    })
    
self.addEventListener('activate', (event) => {
    event.waitUntil(new Promise((resolve, reject) => {
        console.log("doing clean-up stuff!")
        resolve()
    }))
    console.log('activation done!')
})

self.addEventListener('fetch', (event) => {
    console.log("Request intercepted", event)
});

// Apple Specific from:
// https://developer.apple.com/videos/play/wwdc2022/10098/
self.addEventListener('push', (event) => {
    let pushMessageJSON = event.data.json();

    // Our server puts everything needed to show the notification
    // in our JSON data.
    event.waitUntil(self.registration.showNotification(pushMessageJSON.title, { 
        body: pushMessageJSON.body,
        tag: pushMessageJSON.tag,
        actions: [{
            action: pushMessageJSON.actionURL,
            title: pushMessageJSON.actionTitle,
        }]
    }));
})

self.addEventListener('notificationclick', async function(event) {
    if (!event.action)
        return;
    // This always opens a new browser tab,
    // even if the URL happens to already be open in a tab.
    clients.openWindow(event.action);
});