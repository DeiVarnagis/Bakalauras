# Teltonika-praktika


This app was made using [Laravel](http://laravel.com/). Be sure to check [their documentation](laravel.com/docs).

Installation
------------
Back-end
------------

First, you will need to install [Composer](http://getcomposer.org/) following the instructions on their site.

Then, simply run the following command:

```sh
git clone https://github.com/DeiVarnagis/Company-assets-transfer-and-lending-system.git
```

Alternatively, you may download the [(https://github.com/DeiVarnagis/Company-assets-transfer-and-lending-system/archive/main.zip)](https://github.com/DeiVarnagis/Company-assets-transfer-and-lending-system/archive/main.zip) and run `composer install` from your project's root directory.

Configuration
-------------
Modify the .env file to suit your needs

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

When you have the .env with your database connection set up you can run your migrations

```bash
php artisan migrate
```
Now just run one more command to seed your database

```bash
php artisan db:seed
```

After that you can generate your secret
```bash
php artisan jwt:secret
```
Now your app is ready!! Just run last command to start your localhost server!

```bash
php artisan serve
```

You can use [PostMan](https://www.postman.com/) to try out Api calls.

Front-end
------------
First visit [Vue Cli](https://cli.vuejs.org/guide/installation.html) and install it

Then go to front end directory and run

```bash
npm install
```
Next, to run local server 
```bash
npm run serve
```
Now you are ready to use the app. Important!

Dont forget to run you Database and back-end servers before using front-end.
--------------------------------

Api calls
---------------------------------

```bash
No auth routes
---------------------------------------------------
Hedears:
Content-Type:application/json
Accept:application/json

---------------------------------------------------
http://127.0.0.1:8000/api/auth/register

Body -json
{
    "name":"xxxxxx",
    "surname":"xxxxxx",
    "email":"xxxxxx@gmail.com",
    "password":"xxxxxx",
    "password_confirmation":"xxxxxx"
}
---------------------------------------------------
http://127.0.0.1:8000/api/auth/login

Body -json
{
    "email": "xxxxx@gmail.com",
    "password": "xxxxxxx"
}
---------------------------------------------------
http://127.0.0.1:8000/api/email/resend

Body -json
{
    "email":"xxxxxx@gmail.com"
}
---------------------------------------------------
http://127.0.0.1:8000/api/email/verify/{token}

 After registration specific url will be send to user email. 
 Click the url to verify user email.
---------------------------------------------------
http://127.0.0.1:8000/api/password/forgot 

This route will send email with a specific url which will redirect user to http://127.0.0.1:8000/api/password/reset/{token}

Body -json
{
    "email":"xxxxxx@gmail.com"
}
---------------------------------------------------
http://127.0.0.1:8000/api/password/reset/{token}

Don't forget to add body to the request for password reset.

Body -json
{
    "password":"xxxxx",
    "password_confirmation":"xxxxx"
}
-------------------------------------------------------------------
Auth urls - need to be logged!

Hedears:
Content-Type:application/json
Accept:application/json
Authorization: bearer {token}
{token} - you will get token after you login

http://127.0.0.1:8000/api/auth/logout

http://127.0.0.1:8000/api/auth/refresh

http://127.0.0.1:8000/api/auth/user-profile

------------------------------------------------------------------------
Auth - verified routes
User must be logged and have verified account because of the middleware protection.

Devices routes
{type} - Only ShortTerm/LongTerm types will work, otherwise you will get 404 (NotFound).
ShortTerm - devices that most of the times will be used temporary. Code - not unique
LongTerm - devices that most of the times will be used for long-term . Code - unique

Headers:
Content-Type:application/json
Accept:application/json
Authorization: bearer {token} 

http://127.0.0.1:8000/api/devices/{type} - Get
---------------------------------------------------
http://127.0.0.1:8000/api/devices/{type} - Post

Body
{
    "code": "xxxxxxxxx",
    "name": "xxxxxxxx",
    "serialNumber": "xxxxxxxxx",
    "amount": "Number"-> 1 2 3
}
---------------------------------------------------

http://127.0.0.1:8000/api/devices/{type}/{id} - patch

Body
{
    "code": "xxxxxxxxx",
    "name": "xxxxxxxx",
    "serialNumber": "xxxxxxxxx",
    "amount": "Number"-> 1 2 3
}
---------------------------------------------------
http://127.0.0.1:8000/api/devices/{type}/{id} Get
http://127.0.0.1:8000/api/devices/{type}/{id} Delete

---------------------------------------------------------------------------
Devices tranfer routes

http://127.0.0.1:8000/api/devices/transfer Post

user_id -> Id of the user you want to transfer or lend your device.
----------
you can pass only one of following
longTerm_id -> if you want to do actions with long term devices(id of the device)
shortTerm_id -> if you want to do actions with short term devices(id of the device)
--------
action -> 1 means transfer, 2 means lend
Body
{
    "user_id": xx,
    "longTerm_id":xx,
    "action":x (1,2)
}

http://127.0.0.1:8000/api/devices/transfer/confirm/{id} - Patch
{id} - pass id of the transfer request. It will automatically do the operation depending 
on the action(lend, transfer) of the request.

http://127.0.0.1:8000/api/devices/transfer/decline/{id} - Patch
{id} - pass id of the transfer request. It will decline the request.
It can be done by owner and user who will get the device.

------------------------------------------------------------------------
User Devices (filter)

http://127.0.0.1:8000/api/userDevices?queary - Get

------------------------------------------------------------------------
Users api

Update (Put)
http://127.0.0.1:8000/api/users/{id}

body
{
    "name":"qqqqqq",
    "surname":"asdfgdfg",
    "email":"qweqwe@gmail.com",
    "birth":null
}

Messages count (Get)
http://127.0.0.1:8000/api/users/messages/count

User messages(Get)
http://127.0.0.1:8000/api/users/messages

All users(Get)
http://127.0.0.1:8000/api/users

------------------------------------------------------------------------
Accessories Api

All accessories
http://127.0.0.1:8000/api/accessories

Spesific accessory(Get)
http://127.0.0.1:8000/api/accessories/{id}

Update accessory(Put)
http://127.0.0.1:8000/api/accessories/{id}

{
    "name":"sdfasdfasdf",
    "amount":"1",
    "longTerm_id":null,
    "shortTerm_id":4
}

Delete accessory(Delete)
http://127.0.0.1:8000/api/accessories/{id}

Add accessory(Post)
http://127.0.0.1:8000/api/accessories

body
{
    "name":"sdfasdfasdf",
    "amount":"1",
    "longTerm_id":null,
    "shortTerm_id":4
}

------------------------------------------------------------------------
leave work api

Add request to transfer all devices(post)

http://127.0.0.1:8000/api/leaveWork

body
{
    "user_id": 2
}

Confirm request
http://127.0.0.1:8000/api/leaveWork/confirm/{id}

Decline request
http://127.0.0.1:8000/api/leaveWork/decline/1
```

New update -> added email verification and password reset api. 
How to use?
```
First thing to do is to update yours env fail. Example:

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=10f87e52a3540d
MAIL_PASSWORD=500c62abed7b3f
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=6e224e622f-f38aef@inbox.mailtrap.io
MAIL_FROM_NAME="${APP_NAME}""

```
You will get all verification links into your mailtrap.
Now you can use [PostMan](https://www.postman.com/) to try out Api calls.
