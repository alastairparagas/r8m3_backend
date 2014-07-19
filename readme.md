## R8M3

R8M3 is a web and hybrid mobile application (built using Phonegap-Cordova with the Camera plugin). 
This repository will hold the web application and the corresponding API interface that the hybrid 
mobile app can send JSON data to so that it can be stored on our end.

### Before coding:
- `git clone` this repository
- Composer Package Manager: If you have not installed Composer, install it from (https://getcomposer.org/). Composer is an awesome PHP package manager that allows us to install scripts already made by someone else, making our development life easier.
- run `composer install` to get the packages our application uses as defined in composer.json.
- Edit Laravel Configuration
	- Local/Production Environment detection (http://laravel.com/docs/configuration): Edit bootstrap/start.php to have your computer's hostname if not already there so it can be pre-detected for local environment detection.
- Install XAMPP or any choice of LAMP-like PHP stack that works on your OS. Make sure to check your PHP.ini file to have upload_max_filesize set to more than 10mb and have fileinfo.dll extension be enabled (not commented out). If those things are not set, images over 2MB and failure to detect file types will be some errors that you may likely encounter.
- Make sure to have PHP compiler/interpreter as an environment path on your local environment. This allows us to run stuff like phpunit and helpful Laravel stuff in the future right in the command line.

### Before pushing a commit
- This repository comes with Test Files to make sure our code is bug free before committing to this repository. As such, it is tantamount that you 1.) make sure that localhost is on before committing, and 2.) run the tests with `phpunit`.