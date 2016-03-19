<?hh

class OAuthTokenController extends BaseController {
  public static function getPath(): string {
    return '/api/oauth/token';
  }
  
  public static function post(): void {
    $oauth = new OAuth();
    $server = $oauth->getOAuthServer();
    $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    die;
  }
}
