<?hh

class NotifyController extends BaseController {
  public static function getPath(): string {
    return '/notify';
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
    $newConfig->setTitle('Notify');
    return $newConfig;
  }

  public static function get(): :xhp {
    // Get the mailing lists and parse them
    $lists = array("Webmaster Test");
    foreach(UserState::getValues() as $name => $value) {
      array_push($lists, $name);
    }
    $options = <select class="form-control" name="email" />;
    foreach($lists as $list) {
      $options->appendChild(
        <option>{$list}</option>
      );
    }
    return
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Send Notification</h1>
          </div>
          <div class="panel-body">
            <form method="post" action="/notify">
              <div class="form-group">
                <label>Mailing List</label>
                {$options}
              </div>
              <div class="form-group">
                <label>Subject</label>
                <input type="text" class="form-control" name="subject" />
              </div>
              <div class="form-group">
                <label>Body</label>
                <textarea class="form-control" rows={3} name="body"></textarea>
              </div>
              <div class="form-group">
                <label>
                  <input type="checkbox" name="default_footer" checked={false}/> Default Footer
                </label>
              </div>
              <button type="submit" class="btn btn-default">Send</button>
            </form>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    if(!isset($_POST['email']) || !isset($_POST['subject']) || !isset($_POST['body'])) {
      Flash::set('error', 'All fields must be filled out');
      Route::redirect(NotifyController::getPath());
    }

    $default_footer = isset($_POST['default_footer']);
    error_log($default_footer ? 'true' : 'false');

    // Save email to notification log
    if($_POST['email'] == 'Member') {
      NotifyLogMutator::create()
      ->setNotifyTitle($_POST['subject'])
      ->setNotifyText($_POST['body'])
      ->setSenderUserId(Session::getUser()->getID())
      ->_setSentTime(new DateTime(date('Y-m-d H:i')))
      ->save();
    }

    // Find email list
    $emailList = Email::getEmailList($_POST['email']);

    Email::send($emailList, $_POST['subject'], $_POST['body'], $default_footer);
    Flash::set('success', 'Your email was sent successfully');
    Route::redirect(NotifyController::getPath());
  }
}
