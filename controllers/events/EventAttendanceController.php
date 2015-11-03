<?hh

class EventAttendanceController extends BaseController {
  public static function getPath(): string {
    return '/events/attendance/(?<id>\d+)';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Member
        });
    $newConfig->setUserRoles(
      Vector {
        UserRoleEnum::Officer,
        UserRoleEnum::Admin
        });
    $newConfig->setTitle('Events Attendance');
    return $newConfig;
  }

  public static function get(): :xhp {
    $event_id = (int)$_SESSION['route_params']['id'];
    $event = Event::load($event_id);
    if(!$event) {
      Flash::set('error', 'Invalid Event ID');
      Route::redirect(EventsAdminController::getPath());
      invariant(false, "Unreachable");
    }
    $action_url = "/events/attendance/" . $event->getID();
    $curDatetime = new DateTime(date('Y-m-d H:i'));

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
    if($event->getEndDate() < $curDatetime) { // Shouldnt change the values if the event is already over
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
      $user = User::load($attendance->getUserID());
      invariant($user !== null, "Invalid user");
      $actions = <form method="post" action={$action_url}>
                  <button name="change_status" class="btn btn-primary" value={(string) $attendance->getStatus()} type="submit">
                    Change Status
                  </button>
                  <button name="delete" class="btn btn-danger" value={(string) $user->getID()} type="submit">
                    Delete
                  </button>
                  <input type="hidden" name="user_id" value={(string) $user->getID()}/>
                  <input type="hidden" name="event_id" value={(string) $event->getID()}/>
                </form>;
      if($event->getEndDate() < $curDatetime) { // Shouldnt change the values if the event is already over
        $actions = <p/>;
      }
      $table_body->appendChild(
        <tr>
          <td>{$user->getFirstName() . ' ' . $user->getLastName()}</td>
          <td>{$user->getUserStateStr()}</td>
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

    Route::redirect('/events/attendance/' . $_POST['event_id']);
  }
}
