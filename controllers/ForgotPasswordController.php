<?hh //decl

class ForgotPasswordController extends BaseController {
  public static function getPrePath(): string {
    return '/password/';
  }
  public static function getPath(): string {
    return self::getPrePath() . '(?<forgot_token>\w+)';
  }

  public static function get(): :xhp {
  	$forgotToken = $_SESSION['route_params']['forgot_token'];
    $forgot_user = User::loadForgotToken($forgotToken);

    if(!$forgot_user) {
			Flash::set('error', 'Invalid password forgot link');
    	Route::redirect(LoginController::getPath());
    }

    return
  		<div class="well col-md-4 col-md-offset-4">
        <form method="post" action={self::getPrePath() . $forgotToken}>
          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="New password" />
            <p class="help-block">Password must be longer than 6 characters</p>
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" class="form-control" name="password2" placeholder="Confirm new password" />
          </div>
          <button type="submit" class="btn btn-default">Reset</button>
        </form>
      </div>;
  }

  public static function post(): void {
  	$forgotToken = $_SESSION['route_params']['forgot_token'];
    $forgot_user = User::loadForgotToken($forgotToken);

  	if(!$forgot_user || !isset($_POST['password']) || !isset($_POST['password2']) || $_POST['password'] == '' || $_POST['password2'] == '') {
      Flash::set('error', 'All fields are required');
      Route::redirect(self::getPath());
    }

    // Verify password length
    if(strlen($_POST['password']) < 6) {
      Flash::set('error', 'Password must be longer than 6 characters');
      Route::redirect(self::getPath());
    }

    // Verify passwords match
    if($_POST['password'] != $_POST['password2']) {
      Flash::set('error', 'Passwords do not match');
      Route::redirect(self::getPath());
    }

    UserMutator::updatePassword($forgot_user->getID(), $_POST['password']);
    Flash::set('success', 'Password was changed');
     Route::redirect(LoginController::getPath());
  }
}