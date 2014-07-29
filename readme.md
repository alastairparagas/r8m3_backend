## R8M3

R8M3 is a web and hybrid mobile application (built using Phonegap-Cordova).
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
- To get the correct database structure and seed your database with some test data, just follow the Laravel tutorials on seeding and schema. I've already developed the Schema structure and seed files for the database, all you have to do is command-line them.

### Before pushing a commit
- This repository comes with Test Files to make sure our code is bug free before committing to this repository. As such, it is tantamount that you 1.) make sure that localhost is on before committing, and 2.) run the tests with `phpunit`.
- Before running PHPUnit, make sure to import database migrations (http://culttt.com/2013/05/06/laravel-4-migrations/), which automatically creates our database tables and columns for us, and seed that database (http://culttt.com/2013/12/16/seeding-laravel-4-database/) with test data for testability purposes.

### Further documentation
You can see further documentation for certain steps and quirks on developing R8M3 to our repository's Wiki page, which contains more specific information on certain tools used and suggested workflow. Also, if you need help, do not hesitate posting it on the Wiki!