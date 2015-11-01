<?hh

class EventAttendanceController extends BaseController {
  public static function getPath(): string {
    return '/events/(?<id>\d+)';
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
    $event = Event::genByID($event_id);
    if(!$event) {
      Flash::set('error', 'Invalid Event ID');
      Route::redirect(EventsAdminController::getPath());
      invariant(false, "Unreachable");
    }

    $table = <table class="table table-bordered table-striped sortable" />;
    $table->appendChild(
      <thead>
        <tr>
          <th>Name</th>
          <th>Member Status</th>
        </tr>
      </thead>
    );

    $table_body = <tbody />;
    $attendances = Attendance::genAllForEvent($event->getID());
    foreach($attendances as $attendance) {
      $user = User::load($attendance->getUserID());
      invariant($user !== null, "Invalid user");
      $table_body->appendChild(
        <tr>
          <td>{$user->getFirstName() . ' ' . $user->getLastName()}</td>
          <td>{$user->getUserStateStr()}</td>
        </tr>
      );
    }

    $table->appendChild($table_body);

    return
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1>{$event->getName()}</h1>
        </div>
        <div class="panel-body">
          {$table}
        </div>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
        <script src="/js/attendance.js"></script>
      </div>;
  }
}
