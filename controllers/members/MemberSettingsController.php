<?hh //decl

class MemberSettingsController extends BaseController {

  public static function getPath(): string {
    return '/members/settings';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Applicant,
        UserState::Candidate,
        UserState::Pledge,
        UserState::Member,
        UserState::Disabled,
        UserState::Inactive,
        UserState::Alum
        });
    $newConfig->setTitle('User Settings');
    return $newConfig;
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    return
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title">Settings</h1>
        </div>
        <div class="panel-body btn-toolbar">
          <form method="post" action={self::getPath()}>
            <div class="form-group">
              <label>Email</label>
              <input type="email" class="form-control" name="email" placeholder="Email" />
            </div>
            <input type="hidden" name="user_id" id="user_id" value={(string) $user->getID()}/>
            <button type="submit" class="btn btn-default">Update</button>
          </form>
        </div>
      </div>;
  }

  public static function post(): void {
    if($_POST['email'] == '') {
      Flash::set('error', 'All fields are required');
      Route::redirect(self::getPath());
    }

    $idUser = User::load((int) $_POST['user_id']);
    $emailUser = User::loadEmail($_POST['email']);

    if($emailUser && $emailUser->getID() != $idUser->getID()) {
      Flash::set('error', 'Email is already used!');
      Route::redirect(self::getPath());
    }

    UserMutator::update((int) $_POST['user_id'])
      ->setEmail($_POST['email'])
      ->save();

    Route::redirect(MemberProfileController::getPrePath() . $_POST['user_id']);
  }
}
