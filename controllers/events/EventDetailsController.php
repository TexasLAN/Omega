<?hh

class EventDetailsController extends BaseController {
  public static function getPrePath(): string {
    return '/event/';
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
        UserState::Member
    });
    $newConfig->setTitle('Event Details');
    return $newConfig;
  }

  private static function validateActions(Event $event, User $user): bool {
    $curDatetime = new DateTime(date('Y-m-d H:i'));
    return $event->getEndDate() < $curDatetime || !($user->validateRole(UserRoleEnum::Officer) || $user->validateRole(UserRoleEnum::Admin));
  }

  public static function get(): :xhp {
    $user = Session::getUser();
    $event_id = (int)$_SESSION['route_params']['id'];
    $event = Event::load($event_id);
    if(!$event) {
      Flash::set('error', 'Invalid Event ID');
      Route::redirect(EventsAdminController::getPath());
      invariant(false, "Unreachable");
    }

    $descriptionPanel =  <div class="panel panel-default">
      <div class="panel-heading">
        <h1 class="panel-title">Description</h1>
      </div>
      <div class="panel-body">
        <p>{$event->getDescription()}</p>
      </div>
    </div>;


    $action_url = self::getPrePath() . $event->getID();

    $actionPanel = 
    <div class="panel panel-default">
      <div class="panel-heading">
        <h1 class="panel-title">Actions</h1>
      </div>
      <div class="panel-body">
        <form method="post" action={$action_url}>
          <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control" name="email" />
          </div>
          <button name="add_email" class="btn btn-primary" type="submit">
            Add email
          </button>
          <input type="hidden" name="event_id" value={(string) $event->getID()}/>
        </form>
      </div>
    </div>;
    if(self::validateActions($event, $user)) { // Shouldnt change the values if the event is already over or if they dont have the permissions to
        $actionPanel = <p/>;
      }

    $table = <table class="table table-bordered table-striped sortable" />;
    $table->appendChild(
      <thead>
        <tr>
          <th>Name</th>
          <th>Member Status</th>
          <th>Attendance Status</th>
          <th data-defaultsort="disabled">Actions</th>
        </tr>
      </thead>
    );

    $table_body = <tbody />;
    $attendances = Attendance::loadForEvent($event->getID());
    foreach($attendances as $attendance) {
      $load_user = User::load($attendance->getUserID());
      invariant($load_user !== null, "Invalid user");
      $actions = <form class="btn-toolbar" method="post" action={$action_url}>
                  <button name="change_status" class="btn btn-primary" value={(string) $attendance->getStatus()} type="submit">
                    Change Status
                  </button>
                  <button name="delete" class="btn btn-danger" value={(string) $load_user->getID()} type="submit">
                    Delete
                  </button>
                  <input type="hidden" name="user_id" value={(string) $load_user->getID()}/>
                  <input type="hidden" name="event_id" value={(string) $event->getID()}/>
                </form>;
      if(self::validateActions($event, $user)) { // Shouldnt change the values if the event is already over
        $actions = <p/>;
      }
      $table_body->appendChild(
        <tr>
          <td>{$load_user->getFirstName() . ' ' . $load_user->getLastName()}</td>
          <td>{$load_user->getUserStateStr()}</td>
          <td>{($attendance->getStatus() == AttendanceState::Present) ? 'Present' : 'Not Present'}</td>
          <td>
            {$actions}
          </td>
        </tr>
      );
    }

    $table->appendChild($table_body);

    return
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1>{$event->getName()}</h1>
        </div>
        {$descriptionPanel}
        {$actionPanel}
        <div class="panel-body">
          {$table}
        </div>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
        <script src="/js/attendance.js"></script>
      </div>;
  }

  public static function post(): void {
    // Validate User creds
    $user = Session::getUser();
    if(!($user->validateRole(UserRoleEnum::Officer) || $user->validateRole(UserRoleEnum::Admin))) {
      Flash::set('error', 'You do not have the required roles to alter the information');
      Route::redirect(self::getPrePath() . $_POST['event_id']);
    }

    if(isset($_POST['delete'])) {
      AttendanceMutator::deleteUserFromEvent((int) $_POST['user_id'], (int) $_POST['event_id']);
      Flash::set('success', 'Attendance deleted successfully');
    } elseif(isset($_POST['change_status'])) {
      $newStatus = AttendanceState::NotPresent;
      if(((int) $_POST['change_status']) == AttendanceState::NotPresent) {
        $newStatus = AttendanceState::Present;
      }
      AttendanceMutator::updateStatus((int) $_POST['user_id'], (int) $_POST['event_id'], $newStatus);
      Flash::set('success', 'Attendance status changed successfully');
    } elseif(isset($_POST['add_email'])) {
      $addUser = User::loadEmail($_POST['email']);
      if(!$addUser) {
        Flash::set('error', 'Adding the email failed!');
      } else {
        AttendanceMutator::create()
        ->setUserID((int) $addUser->getID())
        ->setEventID((int) $_POST['event_id'])
        ->setStatus(AttendanceState::NotPresent)
        ->save();
        Flash::set('success', 'Adding the email succeeded!');
      }
    }

    Route::redirect(self::getPrePath() . $_POST['event_id']);
  }
}
