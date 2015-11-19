<?hh

// Load in external libraries
require 'vendor/autoload.php';
require 'lib/Config.php';

// Set the app's timezone to central
date_default_timezone_set('America/Chicago');

if(!file_exists('config.ini')) {
  error_log("Config file does not exist");
  die;
}

$configs = parse_ini_file('config.ini', true);
Config::initialize($configs);

// Prepare the databae
DB::$user = $configs['db']['user'];
DB::$password = $configs['db']['password'];
DB::$dbName = $configs['db']['name'];
DB::$port = $configs['db']['port'];

// Setup Sendgrid and email
Email::$sendgrid = new SendGrid($configs['sendgrid']['user'], $configs['sendgrid']['password']);
Email::$from = $configs['sendgrid']['from'];
Email::$webmaster_test = $configs['sendgrid']['webmaster_test'];

// Setup venmo
Venmo::$client_id = $configs['venmo']['client_id'];
Venmo::$client_secret = $configs['venmo']['client_secret'];

// Get the user session going
Session::init();

// Call the dispatcher to do its thing
Route::dispatch(
  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
  $_SERVER['REQUEST_METHOD']
);
