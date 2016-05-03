<?hh

class OAuthAuthorizeController extends BaseController {
  public static function getPath(): string {
    return '/api/oauth/authorize';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Active});
    return $newConfig;
  }

  public static function get(): :xhp {
    $oauth = new OAuth();
    $server = $oauth->getOAuthServer();
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    if (!$server->validateAuthorizeRequest($request, $response)) {
      $response->send();
      exit();
    }
    return
      <div class="well col-md-6 col-md-offset-3">
        <form method="post">
          <h1>{$server->getAuthorizeController()->getClientId()}</h1>
          <h3>would like to access the following info:</h3>
          <ul class="list-group">
            <li class="list-group-item">
              {$server->getAuthorizeController()->getScope()}
            </li>
          </ul>
          <hr />
          <button
            type="submit"
            name="authorized"
            value="yes"
            class="btn btn-primary">
            Authorize
          </button>
          <button
            type="submit"
            name="authorized"
            value="no"
            class="btn btn-default">
            Cancel
          </button>
        </form>
      </div>;
  }
  public static function post(): void {
    $oauth = new OAuth();
    $server = $oauth->getOAuthServer();
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    $is_authorized = (getPOSTString('authorized') === 'yes');
    $user = Session::getUser();
    $server->handleAuthorizeRequest(
      $request,
      $response,
      $is_authorized,
      $user->getID(),
    );
    $response->send();
    exit();
  }
}