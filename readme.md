## R8M3

R8M3 is a web and hybrid mobile application (built using Phonegap-Cordova with the Camera plugin). 
This repository will hold the web application and the corresponding API interface that the hybrid 
mobile app can send JSON data to so that it can be stored on our end.

### Before coding:
- Git clone laravel/laravel into a local folder
- Install composer.phar on that local folder if you already do not have one
- Change dependency on composer.json file from Laravel 4.2 to 4.1 to add PHP 5.3 support 
(Laravel 4.2 only supports PHP 5.4 and above).
- run composer.phar install to get the packages.
- Edit configuration in Laravel
	- Change the encryption key - 32 character string
	- Local/Production Environment detection

