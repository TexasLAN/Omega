<?hh //decl

class MemberProfileController extends BaseController {
  public static function getPrePath(): string {
    return '/members/';
  }
  public static function getPath(): string {
    return self::getPrePath() . '(?<id>\d+)';
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
    $newConfig->setTitle('Profile');
    return $newConfig;
  }

  public static function get(): :xhp {
    $user = Session::getUser();
    $profile_user_id = (int)$_SESSION['route_params']['id'];
    $profile_user = User::load($profile_user_id);
    // Check if valid profile_user
    if(!$profile_user) {
      Flash::set('error', 'Invalid User ID');
      Route::redirect(MemberProfileController::getPrePath() . $user->getID());
      invariant(false, "Unreachable");
    }
    // Check if valid user to view profile page
    if(!($profile_user->getID() == $user->getID() ||
     ($user->validateRole(UserRoleEnum::Admin) || ($user->getState() == UserState::Member && $profile_user->getState() != UserState::Disabled)))) {
      Flash::set('error', 'You do not have permission to view this page');
      Route::redirect(MemberProfileController::getPrePath() . $user->getID());
      invariant(false, "Unreachable");
    }

    $email_hash = md5(strtolower(trim($profile_user->getEmail())));
    $gravatar_url = 'https://secure.gravatar.com/avatar/' . $email_hash . '?s=300';

    $gravatar_change = 
    <div class="caption">
      <p><a href="https://en.gravatar.com/emails/" class="wide btn btn-primary" role="button">Change on Gravatar</a></p>
    </div>;
    if($user->getID() != $profile_user_id) {
      $gravatar_change = <div/>;
    }

    $badges = <p />;
    $badges->appendChild(
      <span class="label label-warning">{ucwords($profile_user->getStateStr())}</span>
    );

    $applicant_info = null;
    if($profile_user->getID() == $user->getID() && $profile_user->getState() == UserState::Applicant) {
      $application = Application::loadByUser($profile_user->getID());
      if(!$application) {
        $application = ApplicationMutator::upsert($profile_user->getID(), '', '', '', '', '', '', '', '' );
      }

      if(!$application->isStarted() && !$application->isSubmitted()) {
        $status = <a href="/apply" class="btn btn-primary btn-lg wide">Start Application</a>;
      } elseif($application->isStarted() && !$application->isSubmitted()) {
        $status = <a href="/apply" class="btn btn-primary btn-lg wide">Finish Application</a>;
      } else {
        $status = <h3>Application Status: <span class="label label-info">Under review</span></h3>;
      }
      $applicant_info =
        <div class="panel-body">
          {$status}
        </div>;
    }

    $memberInfo = null;
    if($profile_user->getState() != UserState::Disabled) {
      $gmPresent = Attendance::countUserAttendance($profile_user->getID(), AttendanceState::Present, EventType::GeneralMeeting);
      $gmNotPresent = Attendance::countUserAttendance($profile_user->getID(), AttendanceState::NotPresent, EventType::GeneralMeeting);
      $omPresent = Attendance::countUserAttendance($profile_user->getID(), AttendanceState::Present, EventType::OfficerMeeting);
      $omNotPresent = Attendance::countUserAttendance($profile_user->getID(), AttendanceState::NotPresent, EventType::OfficerMeeting);
      $memberInfo =
          <div class="panel panel-default">
            <div class="panel-heading">
              <h1 class="panel-title">Member Information</h1>
            </div>
            <div class="panel-body">
              <h5>Email: </h5>
              <p>{$profile_user->getEmail()}</p>
              <h5>Phone Number: </h5>
              <p>{$profile_user->getPhoneNumber()}</p>
              <h5>General Meeting Attendance: </h5>
              <p>{$gmPresent . " / " . ($gmPresent + $gmNotPresent)}</p>
              <h5>Officer Meeting Attendance: </h5>
              <p>{$omPresent . " / " . ($omPresent + $omNotPresent)}</p>
            </div>
          </div>;
    }

    $events = null;
    if($profile_user->getState() != UserState::Disabled) {
      $events = Event::loadFuture();
      if(!empty($events)) {
        $events =
          <div class="panel panel-default">
            <div class="panel-heading">
              <h1 class="panel-title">Upcoming Events</h1>
            </div>
            <div class="panel-body">
              <omega:event-list events={$events} />
            </div>
          </div>;
      }
    }

    $roles = $profile_user->getRoles();
    foreach($roles as $role) {
      $badges->appendChild(<span class="label label-success">{ucwords($role)}</span>);
    }

    return
      <x:frag>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="col-md-3">
              <div class="thumbnail">
                <img src={$gravatar_url} class="img-thumbnail" />
                {$gravatar_change}
              </div>
            </div>
            <div class="col-md-9">
              <h1>{$profile_user->getFirstName() . ' ' . $profile_user->getLastName()}</h1>
              {$badges}
              {$memberInfo}
            </div>
          </div>
          {$applicant_info}
        </div>
        {$events}
      </x:frag>;
  }
}
