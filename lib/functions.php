<?hh

function getGETParams(): Map<string, mixed> {
  return new Map($_GET);
}

function getPOSTParams(): Map<string, mixed> {
  return new Map($_POST);
}

function getFILESParams(): Map<string, Map<string, mixed>> {
  return new Map($_FILES);
}

function getRouteParams(): Map<string, string> {
  return new Map($_SESSION['route_params']);
}
