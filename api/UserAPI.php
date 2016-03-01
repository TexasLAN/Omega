<?hh

class UserAPI {
  public static function get(): Map {
    $oauth = new OAuth();
    $server = $oauth->getOAuthServer();
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    $scopeRequired = 'userprofile';
    if (!$server->verifyResourceRequest($request, $response, $scopeRequired)) {
      $response->send();
    }

    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
    $user_id = $token['user_id'];
    $user = User::load($user_id);
    invariant($user !== null, "User should not be null");
    return Map {
      'id' => $user->getID(),
      'username' => $user->getUsername(),
      'email' => $user->getEmail(),
      'fullname' => $user->getFullName()
    };
  }
}
