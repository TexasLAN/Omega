<?hh

class FrontpageController extends BaseController {
  public static function getPath(): string {
    return '/';
  }

  public static function get(): :xhp {
    // If a user is logged in, redirect them to where they belong
    if(Session::isActive()) {
      $user = Session::getUser();
      Route::redirect(MemberProfileController::getPrePath() . $user->getID());
    }

    return
      <div class="col-md-6 col-md-offset-3 masthead">
        <div id="crest"></div>
        <p><a id="signin" class="btn btn-default" role="button" href={LoginController::getPath()}>Login</a></p>
        <p><a id="signup" class="btn btn-default" role="button" href={SignupController::getPath()}>Sign Up</a></p>
      </div>;
  }
}
