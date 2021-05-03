
<h2><strong>Laravel drive sync- multi-structure folder.</strong></h2>

Work Flow:- *Structure: <h2><strong>Root->Parent folder->Workspace folder->Task folder->Task folder files.</h2></strong>

$upload_array = ['default-Workplace' => [
'Test1' => [
'0' => "tasklistfiles/2530/tasklist_216190942753.png",
'1' => "tasklistfiles/2530/tasklist_516190942791.txt"
],
'Test2' => [
'0' => "tasklistfiles/2550/tasklist_216190882045.txt"
]
],
'Workplace' => [
'Test3' => [
'0' => "tasklistfiles/2533/tasklist_216190942753.png",
'1' => "tasklistfiles/2533/tasklist_516190942791.txt"
],
'Test4' => [
'0' => "tasklistfiles/2555/tasklist_216190882045.txt"
]
]
];


<h2><strong>steps:</strong></h2><p>
1- Login into drive(If new user), save the access-token json in DB. <br/>
2- Check if access-token active, if not active refresh access token and update json in DB.<br/>
3- Check if Parent folder exsist, if YES - step4, if NO - step5<br/>
4- Get Parent folder Id Name, go to-step6<br/>
5- Create Parent folder, get folder Id Name, go to-step6<br/>
6- Get folder List, Check if Workspace folder exsist, if YES - step7, if NO - step8<br/>
7- Get Workspace folder id Name, go to-step9<br/>
8- Create Workspace folder, get folder Id Name, go to-step9<br/>
9- Get folder List,Check if Task folder exsist, if YES - step10, if NO - step11<br/>
10- Get Task folder id Name, go to-step12<br/>
11- Create Task folder, get folder Id Name, go to-step12<br/>
12- Get File List, Check if Task file exsist, if YES - step13, if NO- (msg-uploaded previously)<br/>
13- Upload file in specific folder<br/>
 * If while checking folder/file exsist or not, if function return empty array then create
  and make specific upload will be the starting phase. </p>
  
  <br/>
  "require":<br/>
    "google/apiclient": "2.0.*"<br/>

<b> .env</b>
GOOGLE_APP_ID=xxxxx <br/>
GOOGLE_CLIENT_ID=xxxxx <br/>
GOOGLE_CLIENT_SECRET=xxxxxxx <br/>
GOOGLE_REDIRECT='http://localhost:8000/glogin' <br/>

<br/>
<h2><strong>Getting your Client ID and Secret</h2></strong>
[https://console.developers.google.com/]
  

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).


## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for helping fund on-going Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](http://patreon.com/taylorotwell):

- **[Vehikl](http://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Styde](https://styde.net)**
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
