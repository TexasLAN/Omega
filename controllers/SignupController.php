<?hh //decl

class SignupController extends BaseController {
  public static function getPath(): string {
    return '/signup';
  }

  public static function get(): :xhp {

    if(Session::isActive()) {
      Route::redirect(DashboardController::getPath());
    }

    return
      <div class="well col-md-4 col-md-offset-4">
        <form method="post" action="/signup">
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="uname" placeholder="Username" />
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password" />
            <p class="help-block">Password must be longer than 6 characters</p>
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" class="form-control" name="password2" placeholder="Confirm password" />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" placeholder="Email" />
          </div>
          <div class="form-group">
            <label>First Name</label>
            <input type="text" class="form-control" name="fname" placeholder="First Name" />
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" class="form-control" name="lname" placeholder="Last Name" />
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>;
  }

  public static function post(): void {
    if($_POST['uname'] == '' || $_POST['password'] == '' ||
       $_POST['password2'] == '' || $_POST['email'] == '' ||
       $_POST['fname'] == '' || $_POST['lname'] == '') {
      Flash::set('error', 'All fields are required');
      Route::redirect(self::getPath());
    }

    # Verify password length
    if(strlen($_POST['password']) < 6) {
      Flash::set('error', 'Password must be longer than 6 characters');
      Route::redirect(self::getPath());
    }

    # Verify passwords match
    if($_POST['password'] != $_POST['password2']) {
      Flash::set('error', 'Passwords do not match');
      Route::redirect(self::getPath());
    }

    # Create the user
    $user = UserMutator::createUser(
      $_POST['uname'],
      $_POST['password'],
      $_POST['email'],
      $_POST['fname'],
      $_POST['lname']
    );

    # User creation failed
    if(!$user) {
      Flash::set('error', 'Username or Email is taken');
      Route::redirect(self::getPath());
    }

    Session::create($user);
    Route::redirect(DashboardController::getPath());
  }
}
