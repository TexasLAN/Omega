<?hh

class LoginController extends BaseController {
  public static function getPath(): string {
    return '/login';
  }

  public static function get(): :xhp {
    // Check to see if we're going to perform an action
    $query_params = array();
    parse_str($_SERVER['QUERY_STRING'], $query_params);
    if (isset($query_params['action'])) {
      // Log the user out
      if ($query_params['action'] === 'logout') {
        Auth::logout();
        Route::redirect(FrontpageController::getPath());
      }
    }

    if (Session::isActive()) {
      $user = Session::getUser();
      Route::redirect(MemberProfileController::getPrePath().$user->getID());
    }

    return
      <div class="well col-md-4 col-md-offset-4">
        <form method="post" action="/login">
          <div class="form-group">
            <label>Username</label>
            <input
              type="text"
              class="form-control"
              name="username"
              placeholder="Username"
            />
          </div>
          <div class="form-group">
            <label>Password</label>
            <input
              type="password"
              class="form-control"
              name="password"
              placeholder="Password"
            />
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="remember" /> Remember Me
            </label>
          </div>
          <div class="btn-toolbar">
            <button
              type="submit"
              name="action"
              value="login"
              class="btn btn-default">
              Login
            </button>
            <button
              type="submit"
              name="action"
              value="forgot"
              class="btn btn-danger">
              Forgot Password
            </button>
          </div>
        </form>
      </div>;
  }

  public static function post(): void {
    // Make sure all required fields were filled out
    if (!isset($_POST['action']) ||
        !isset($_POST['username']) ||
        !isset($_POST['password'])) {
      Route::redirect(LoginController::getPath());
    }

    switch ($_POST['action']) {
      case 'login':
        // Authenticate
        if (!Auth::login($_POST['username'], $_POST['password'])) {
          Flash::set('error', 'Login failed');
          Route::redirect(LoginController::getPath());
        }

        // Redirect to where we need to go
        $user = Session::getUser();
        if (!$user) {
          Route::redirect(LoginController::getPath());
        } else {
          if (Flash::exists('redirect')) {
            Route::redirect((string) Flash::get('redirect'));
          }
          if (isset($_POST['remember'])) {
            Auth::rememberMe();
          }

          // Logged in correctly
          Route::redirect(
            MemberProfileController::getPrePath().$user->getID(),
          );
        }
        break;
      case 'forgot':
        $result = Auth::requestPasswordReset($_POST['username']);
        if ($result) {
          Flash::set('success', 'Check your email for the password reset');
        } else {
          Flash::set(
            'error',
            'There was an error setting that acccount for a reset',
          );
        }
        Route::redirect(LoginController::getPath());
        break;
      default:
        Route::redirect(LoginController::getPath());
        break;
    }
  }
}
