# Web Push (Proof of Concept)
Place this app in **nextcloud/apps/**
- ```nextcloud/apps/$ git clone ...```

## General

- High Level Info: 
  - Apple Developer [VIDEO](https://developer.apple.com/videos/play/wwdc2022/10098/) that introduced the Feature for macOS Safari last year
  - https://webkit.org/blog/12945/meet-web-push/
  - Official SSL Cert for Nextcloud needed!
- Lower Level Info: 
  - Apple [Sending web push notifications in web apps, Safari, and other browsers](https://developer.apple.com/documentation/usernotifications/sending_web_push_notifications_in_web_apps_safari_and_other_browsers)
  -  Great Detail Articles
      - General: https://web.dev/push-notifications-web-push-protocol 
      - Send https://web.dev/sending-messages-with-web-push-libraries/ 
      - Receive https://web.dev/push-notifications-handling-messages/ 
      - ...

![image](https://web-dev.imgix.net/image/C47gYyWYVMMhDmtYSLOWazuyePF2/jjHOGQvZttcOEij3c6UR.svg)

## Test on CLI
  
- Create VAPID + Keys + Headers + Claims upfront and test it on CLI
    - Multiple Platforms: https://github.com/web-push-libs/
    - create VAPID: https://github.com/web-push-libs/vapid    
- send via CLI: https://github.com/web-push-libs/pywebpush
    - it handles authorization, encryption, ...
    - ```bin/python pywebpush --data data.json --info subscription.json --key private_key.pem --head header.json --v --claims claims.google.json```
 
data.json: 
```
{
    "title": "This is the title",
    "body": "This is the body"
}
 ```

claims.google.json:
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