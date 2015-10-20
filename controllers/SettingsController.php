<?hh

class SettingsController extends BaseController {
  public static function getPath(): string {
    return '/settings';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Member
        });
    $newConfig->setUserRoles(
      Vector {
        UserRoleEnum::Admin
        });
    $newConfig->setTitle('Events Checkin');
    return $newConfig;
  }

  public static function get(): :xhp {
    $applications_open = Settings::get('applications_open');
    return
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Send Notification</h1>
          </div>
          <div class="panel-body">
            <form class="form" action="/settings" method="post">
              <div class="form-group">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="applications_disabled" checked={!$applications_open}/> Disable Applications
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
    if(isset($_POST['applications_disabled'])) {
      Settings::set('applications_open', false);
    } else {
      Settings::set('applications_open', true);
    }

    Route::redirect(SettingsController::getPath());
  }
}
