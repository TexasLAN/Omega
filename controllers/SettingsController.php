<?hh

class SettingsController extends BaseController {
  public static function getPath(): string {
    return '/settings';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Active});
    $newConfig->setUserRoles(Vector {UserRoleEnum::Admin});
    $newConfig->setTitle('Site Settings');
    return $newConfig;
  }

  public static function get(): :xhp {
    $cur_class = Settings::getCurrentClass();
    $applications_open = Settings::get('applications_open');
    $voting_open = Settings::get('voting_open');

    // Average Attendance of all actives combined
    $counter = 0;
    $LannieAttendance = 0.0;
    $userList = DB::query("SELECT * FROM users where member_status=2");
    foreach ($userList as $row_user) {
      $counter++;
      $eventPresent = Attendance::countUserAttendance(
        (int) $row_user['id'],
        AttendanceState::Present,
        NULL,
      );
      $eventNotPresent = Attendance::countUserAttendance(
        (int) $row_user['id'],
        AttendanceState::NotPresent,
        NULL,
      );
      $eventPercent =
        ($eventPresent / ($eventPresent + $eventNotPresent)) * 100;

      $LannieAttendance += (($eventPercent - $LannieAttendance) / $counter);
    }

    // Get selected toggle on selected dropdown
    $cur_class_div = <div />;
    $cur_class_div->appendChild(<p>Current Class:</p>);
    $select_class = <select name="cur_class"></select>;
    foreach (LanClass::getValues() as $name => $value) {
      if ($value == $cur_class) {
        $select_class->appendChild(
          <option value={(string) $value} selected={true}>{$name}</option>,
        );
      } else {
        $select_class->appendChild(
          <option value={(string) $value}>{$name}</option>,
        );
      }
    }
    $cur_class_div->appendChild($select_class);

    return
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Settings</h1>
          </div>
          <div class="panel-body">
            <p>{number_format($LannieAttendance, 2, '.', '')}</p>
            <form class="form" action="/settings" method="post">
              <div class="form-group">
                {$cur_class_div}
                <div class="checkbox">
                  <label>
                    <input
                      type="checkbox"
                      name="applications_disabled"
                      checked={!$applications_open}
                    />
                    Disable Applications
                  </label>
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {

    if (isset($_POST['cur_class'])) {
      Settings::set('cur_class', $_POST['cur_class']);
    }

    if (isset($_POST['applications_disabled'])) {
      Settings::set('applications_open', false);
    } else {
      Settings::set('applications_open', true);
    }

    Flash::set('success', 'Setings updated successfully');
    Route::redirect(SettingsController::getPath());
  }
}
