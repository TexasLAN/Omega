<?hh

class EventDetailsController extends BaseController {
  public static function getPrePath(): string {
    return '/event/';
  }

  public static function getPath(): string {
    return self::getPrePath().'(?<id>\d+)';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Applicant,
        UserState::Candidate,
        UserState::Pledge,
        UserState::Active,
      },
    );
    $newConfig->setTitle('Event Details');
    return $newConfig;
  }

  private static function validateActions(Event $event, User $user): bool {
    $curDatetime = new DateTime(date('Y-m-d H:i'));
    return
      $event->getEndDate() >= $curDatetime &&
      ($user->validateRole(UserRoleEnum::Officer) ||
       $user->validateRole(UserRoleEnum::Admin));
    // return
    //   ($user->validateRole(UserRoleEnum::Officer) ||
    //    $user->validateRole(UserRoleEnum::Admin));
  }

  public static function get(): :xhp {
    $user = Session::getUser();
    $event_id = (int) $_SESSION['route_params']['id'];
    $event = Event::load($event_id);
    if (!$event) {
      Flash::set('error', 'Invalid Event ID');
      Route::redirect(EventsAdminController::getPath());
      invariant(false, "Unreachable");
    }

    $descriptionPanel =
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title">Description</h1>
        </div>
        <div class="panel-body">
          <p>{$event->getDescription()}</p>
        </div>
      </div>;

    $full_user_list = User::loadForAutoComplete();
    $user_list = array();
    foreach ($full_user_list as $row) {
      $row_data = Map {};
      $row_data->add(
        Pair {'value', $row->getFullName().' ('.$row->getEmail().')'},
      )->add(Pair {'data', (string) $row->getID()});

      array_push($user_list, $row_data);
    }

    $action_url = self::getPrePath().$event->getID();
    $actionPanel = <div />;

    if (self::validateActions($event, $user)) {
      $actionPanel =
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Actions</h1>
          </div>
          <div class="panel-body">
            <form method="post" action={$action_url}>
              <div id="searchfield" class="form-group">
                <input
                  type="text"
                  class="form-control biginput"
                  id="autocomplete"
                />
              </div>
              <div class="btn-toolbar">
                <button
                  name="mark_present_user_btn"
                  class="btn btn-primary"
                  type="submit">
                  Mark Present
                </button>
                <button
                  name="mark_not_present_user_btn"
                  class="btn btn-primary"
                  type="submit">
                  Mark Not Present
                </button>
                <button
                  name="mark_excused_user_btn"
                  class="btn btn-primary"
                  type="submit">
                  Mark Excused
                </button>
              </div>
              <input
                type="hidden"
                id="user_list"
                value={json_encode($user_list)}
              />
              <input
                type="hidden"
                name="event_id"
                value={(string) $event->getID()}
              />
              <input type="hidden" id="user_id" name="mark_user" value="0" />
            </form>
          </div>
        </div>;
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
    foreach ($attendances as $attendance) {
      $load_user = User::load($attendance->getUserID());
      invariant($load_user !== null, "Invalid user");
      $actions = <div />;
      if (self::validateActions($event, $user)) { // Shouldnt change the values if the event is already over
        $actions =
          <form class="btn-toolbar" method="post" action={$action_url}>
            <button
              name="change_status"
              class="btn btn-primary"
              value={(string) $attendance->getStatus()}
              type="submit">
              Change Status
            </button>
            <button
              name="delete"
              class="btn btn-danger"
              value={(string) $load_user->getID()}
              type="submit">
              Delete
            </button>
            <input
              type="hidden"
              name="user_id"
              value={(string) $load_user->getID()}
            />
            <input
              type="hidden"
              name="event_id"
              value={(string) $event->getID()}
            />
          </form>;
      }

      $table_body->appendChild(
        <tr>
          <td>{$load_user->getFullName()}</td>
          <td>{$load_user->getStateStr()}</td>
          <td>
            {AttendanceStateInfo::toString(
              AttendanceState::assert($attendance->getStatus()),
            )}
          </td>
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
          <h1>{$event->getName() . ' - ' . $event->getNeededPoints()}</h1>
        </div>
        {$descriptionPanel}
        {$actionPanel}
        <div class="panel-body">
          {$table}
        </div>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
        <script src="/js/jquery-1.9.1.min.js"></script>
        <script src="/js/jquery.autocomplete.min.js"></script>
        <script src="/js/attendance.js"></script>
      </div>;
  }

  public static function post(): void {
    // Validate User creds
    $user = Session::getUser();
    if (!($user->validateRole(UserRoleEnum::Officer) ||
          $user->validateRole(UserRoleEnum::Admin))) {
      Flash::set(
        'error',
        'You do not have the required roles to alter the information',
      );
      Route::redirect(self::getPrePath().$_POST['event_id']);
    }

    if (isset($_POST['delete'])) {
      // Delete
      AttendanceMutator::deleteUserFromEvent(
        (int) $_POST['user_id'],
        (int) $_POST['event_id'],
      );
      Flash::set('success', 'Attendance deleted successfully');
    } else if (isset($_POST['change_status'])) {
      // Change Status
      $user = User::load((int) $_POST['user_id']);
      $event = Event::load((int) $_POST['event_id']);
      if(is_null($user) || is_null($event) || $user->getTotalPoints() < $event->getNeededPoints()) {
        Flash::set('error', 'Changing the users status failed! They dont have enough points!');
        Route::redirect(self::getPrePath().$_POST['event_id']);
      }

      $newStatus = AttendanceState::NotPresent;
      if (((int) $_POST['change_status']) == AttendanceState::NotPresent) {
        $newStatus = AttendanceState::Present;
      }
      AttendanceMutator::updateStatus(
        (int) $_POST['user_id'],
        (int) $_POST['event_id'],
        $newStatus,
      );
      Flash::set('success', 'Attendance status changed successfully');
    } else if (isset($_POST['mark_user'])) {
      // Mark
      $attendanceState = AttendanceState::NotPresent;
      if (isset($_POST['mark_present_user_btn'])) {
        $attendanceState = AttendanceState::Present;
      } else if (isset($_POST['mark_excused_user_btn'])) {
        $attendanceState = AttendanceState::Excused;
      }
      $addUser = User::load((int) $_POST['mark_user']);
      $event = Event::load((int) $_POST['event_id']);
      if(is_null($addUser) || is_null($event) || $addUser->getTotalPoints() < $event->getNeededPoints()) {
        Flash::set('error', 'Marking the user failed! They dont have enough points!');
        Route::redirect(self::getPrePath().$_POST['event_id']);
      }
      $eventUserAttend =
        ($addUser)
          ? Attendance::loadForUserEvent(
            $addUser->getID(),
            (int) $_POST['event_id'],
          )
          : null;
      if (!$addUser) {
        Flash::set('error', 'Marking the user failed!');
      } else if (!$eventUserAttend) {
        // Need to Add user
        AttendanceMutator::create()
          ->setUserID((int) $addUser->getID())
          ->setEventID((int) $_POST['event_id'])
          ->setStatus($attendanceState)
          ->save();
        Flash::set(
          'success',
          'Adding user as '.
          AttendanceStateInfo::toString($attendanceState).
          ' succeeded!',
        );
      } else {
        if ($eventUserAttend->getStatus() == $attendanceState) {
          Flash::set(
            'error',
            'User has already been marked as '.
            AttendanceStateInfo::toString($attendanceState).
            '!',
          );
        } else {
          AttendanceMutator::updateStatus(
            $eventUserAttend->getUserID(),
            $eventUserAttend->getEventID(),
            $attendanceState,
          );
          Flash::set(
            'success',
            'Marking the user as '.
            AttendanceStateInfo::toString($attendanceState).
            ' succeeded!',
          );
        }
      }
    }

    Route::redirect(self::getPrePath().$_POST['event_id']);
  }
}
