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
        UserState::Active,
        UserState::Disabled,
        UserState::Inactive,
        UserState::Alum
        });
    $newConfig->setTitle('User Settings');
    return $newConfig;
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    // Update Venmo if applied
    // if(isset($_GET['code'])) {
    //   $venmo_code = $_GET['code'];
    //   $access_obj = Venmo::exchangeToken($venmo_code);

    //   if(isset(json_decode($access_obj)->access_token)) {
    //     $durationEndTime = new DateTime(date(DateTime::ISO8601));
    //     date_add($durationEndTime, DateInterval::createFromDateString(json_decode($access_obj)->expires_in . ' seconds'));
    //     error_log(serialize(json_decode($access_obj)));
    //     error_log($durationEndTime->format(DateTime::ISO8601));
    //     UserMutator::update($user->getID())
    //       ->setVenmoToken(json_decode($access_obj)->access_token)
    //       ->setVenmoRefresh(json_decode($access_obj)->refresh_token)
    //       ->_setVenmoDuration($durationEndTime)
    //       ->save();
    //   }
    // }

    // $disabledLoginButton = '';
    // if(isset($user->_getVenmoToken())) {
    //   $disabledLoginButton = ' disabled';
    // }

    return
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title">Settings</h1>
        </div>
        <div class="panel-body btn-toolbar">
          <form method="post" action={self::getPath()}>
            <div class="form-group">
              <label>Email</label>
              <input type="email" class="form-control" name="email" placeholder="Email" value={$user->getEmail()}/>
            </div>
            <div class="form-group">
              <label>Phone Number</label>
              <input type="text" class="form-control" name="phone" placeholder="Phone Number" value={$user->getPhoneNumber()}/>
            </div>
            <input type="hidden" name="user_id" id="user_id" value={(string) $user->getID()}/>
            <button type="submit" class="btn btn-default">Update</button>
          </form>
        </div>
      </div>;
  }

  public static function post(): void {
    if($_POST['email'] == '' || $_POST['phone'] == '') {
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
      ->setPhoneNumber($_POST['phone'])
      ->save();

    Route::redirect(MemberProfileController::getPrePath() . $_POST['user_id']);
  }
}
