<?hh

# Load in external libraries
require 'vendor/autoload.php';

# Set the app's timezone to central
date_default_timezone_set('America/Chicago');

if(!file_exists('config.ini')) {
  error_log("Config file does not exist");
  die;
}

$configs = parse_ini_file('config.ini', true);

# Setup Mailgun and email
use Mailgun\Mailgun;
Email::$mg = new Mailgun($configs['mailgun']['key']);
Email::$domain = $configs['mailgun']['domain'];
Email::$from = $configs['mailgun']['from'];

# Prepare the databae
DB::$user = $configs['db']['user'];
DB::$password = $configs['db']['password'];
DB::$dbName = $configs['db']['name'];
DB::$port = $configs['db']['port'];

# Get the user session going
Session::init();

# Call the dispatcher to do its thing
Route::dispatch(
  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
  $_SERVER['REQUEST_METHOD']
);
