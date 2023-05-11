# Web Push (Nextcloud App)

## General

⚠️ **This is only a Proof of Concept, use at your own risk** ⚠️

ℹ️ What WebPush is: [Video](https://developer.apple.com/videos/play/wwdc2022/10098/) (0:00 - 1:15 minutes)

🚧 At this stage you can use this App only to..
  - send WebPush with `occ` CLI tool ✅
  - or integrate it yourself into your other Nextcloud App Sourcecode
  - or extend this app yourself to serve as kind of (REST) API

🚫 It is not..
  - able to send out every usual Notification as WebPush, therefore the Nextcloud/Notifications App or Pushproxy would have to be altered (perhaps [see here](https://github.com/nextcloud/notifications/issues/1225))

## Requirements
- Clientside: 
    - Apple:
        - iOS 16.4+ needed
        - The web app must be installed on the iOS home screen
    - ...
- Server:
    - Official SSL Certificate for Nextcloud!
    - PHP Version + Modules according to: https://github.com/web-push-libs/web-push-php#requirements

## Installation
Place this app in **.../apps/** like so:
- `.../apps/$ git clone https://github.com/githubkoma/webpush.git`
- Adjust permissions to match the other folders+files in `.../apps/*`
- Enable the App in Nextcloud
- Set Your Admin E-Mail Adress in Nextcloud Personal Settings
- Go To Nextcloud Admin Settings -> Web Push -> "Generate new Vapid Keys"
  - CAUTION: Best Case is you only use this button once after Installation. Because after using this button again, EVERY User has to re-subscribe!
- Test from CLI: `sudo -u www-data php occ webpush:generate admin "hello"`
- Proceed with Usage (below)

## Usage
0. Make sure _REQUIREMENTS_ are met and _INSTALLATION_ is finished (both see above ⬆️)
1. As a User you visit your Nextcloud Settings 
2. On the Left side choose the Section "Web Push"
3. Click on "Subscribe to Webpush" twice _(and be patient)_    
4. `"My WebPush Subscription:"` changes from `"empty"` to something like `"...fy3R3F"`
5. You will get a test Notification, that states your success
    - _(if not, contact your nextcloud admin)_

## Dev Info

- High Level Info: 
    - Apple Developer [VIDEO](https://developer.apple.com/videos/play/wwdc2022/10098/) that introduced the Feature for macOS Safari last year
    - https://webkit.org/blog/12945/meet-web-push/  
- Lower Level Info: 
  - Apple [Sending web push notifications in web apps, Safari, and other browsers](https://developer.apple.com/documentation/usernotifications/sending_web_push_notifications_in_web_apps_safari_and_other_browsers)
  -  Great Detail Articles
      - General: https://web.dev/push-notifications-web-push-protocol 
      - Send https://web.dev/sending-messages-with-web-push-libraries/ 
      - Receive https://web.dev/push-notifications-handling-messages/ 
      - ...

![image](https://web-dev.imgix.net/image/C47gYyWYVMMhDmtYSLOWazuyePF2/jjHOGQvZttcOEij3c6UR.svg)

### Test WebPush with Python
  
- Create VAPID + Keys + Headers + Claims upfront and test it on CLI
    - Multiple Platforms: https://github.com/web-push-libs/
    - create VAPID: https://github.com/web-push-libs/vapid
      - run `bin/vapid` ...
      - as well as creating `header.json`, `data.json`, `claims.json`, `subscription.json`, ...
- send via Python: https://github.com/web-push-libs/pywebpush
    - it handles authorization, encryption, ...
    - ```bin/python pywebpush --data data.json --info subscription.json --key private_key.pem --head header.json --v --claims claims.json```

Keypair:
```
openssl ecparam -genkey -name prime256v1 -out private_key.pem
openssl ec -in private_key.pem -pubout -outform DER|tail -c 65|base64|tr -d '=' |tr '/+' '_-' >> public_key.txt
openssl ec -in private_key.pem -outform DER|tail -c +8|head -c 32|base64|tr -d '=' |tr '/+' '_-' >> private_key.txt
```
 
data.json: 
```
{
    "title": "This is the title",
    "body": "This is the body"
}
 ```

claims.json:
```
{
    "sub": "mailto:admin@example.com",
    "aud": "https://fcm.googleapis.com/",
    "exp": 1683101017
}
```

header.json
```
{"Authorization": "vapid t=eyJ0eXAiOiJKVjoibWFpbHRvOmFkbWluQHdl1QiLCJhbGciOiJFUzI1NiJ9.eyJhdWQiOiJodHRwczovL3B1c2guc2VydmljZXMubW96aWxsYS5jb20iLCJleHAiOiIxNjgzMTAxMDEitgznbPCiPXMNEwO93Iiwic3ViIeWNsb3VkL0Tfd6bSlJlMYiz9Jv97hPIzjLU6GySAMmRlIn0.NzgCOvNKY_9CL5hbQj9LsLOEvGjmocxFMkSrabnDUVBJFYgfNX8j2w-HzRQzItEIfXas7_8CqC8cPpJncGXfQ4w,k=BPsGDOSf9gwgVQm0JJiozSnzCkcWQYwN7D1uo"}
```

subscription.json
```
 {"endpoint":"https://fcm.googleapis.com/fcm/send/enlz59YzXHB4q:APA91bFx-q7SaVPa8tEpLUvccKNfF4TrbaLbMwQlMx4FajGnwPWZMuje6TQsqAsqTevezMTCX0WeoUKZNqTCStgJCqsLb_Tgji9sKCuQ_fP0ayPFtP7mmeleWm8EP0RinQ2Wa4wPjl","expirationTime":null,"keys":{"p256dh":"BAOw-9bykz2_c00jsWEYifX5sonXho8NJy0EyuD96XQSVrVlrVYCziJVD3KRQV_oY70kk-fqSwGLB7ZFBJO-1lM","auth":"6XW0HmBwjUFseX2xIIryKQ"}}
```

private_key.pem
```
-----BEGIN PRIVATE KEY-----
MCCqGSM49AIGHyqGSM49AgEGwEHBG0wawIBAAgEAMBMGBQQglCHlId7ZBI75bKj2
eTsROmEHqM0p8wpwaFve4TyM4y1DtBUwBp8CrvAxN1gBgzkn/YMIAjrzSmP/Qi+YW0I/S7F
UJtCSYHNE3GhRANCAAT73em0pSZTGIs/SbwDOhskkGMDew9bq
-----END PRIVATE KEY-----
```