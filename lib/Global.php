<?hh

// GET
function getGETParams(): Map<string, mixed> {
  return new Map($_GET);
}

function getGETString(string $key): ?string {
	if (!array_key_exists($key, $_GET)) {
    return null;
  }
  $value = $_GET[$key];
  invariant(is_string($value), 'GET param must be a string');
  return $value;
}

// POST
function getPOSTParams(): Map<string, mixed> {
  return new Map($_POST);
}

function getPOSTString(string $key): (string, bool) {
	if (!array_key_exists($key, $_POST)) {
    return tuple('', true);
  }
  $value = $_POST[$key];
  invariant(is_string($value), 'POST param must be a string');
  return tuple($value, false);
}

// FILES
function getFILESParams(): Map<string, Map<string, mixed>> {
  return new Map($_FILES);
}

// Route
function getRouteParams(): Map<string, string> {
  return new Map($_SESSION['route_params']);
}

function getRouteParamString(string $key): ?string {
	if (!array_key_exists($_SESSION['route_params'], $_POST)) {
    return null;
  }
  $value = $_SESSION['route_params'][$key];
  invariant(is_string($value), 'Route param must be a string');
  return $value;
}

function getRouteParamInt(string $key): ?int {
	if (!array_key_exists($_SESSION['route_params'], $_POST)) {
    return null;
  }
  $value = $_SESSION['route_params'][$key];
  invariant(is_int($value), 'Route param must be an int');
  return $value;
}