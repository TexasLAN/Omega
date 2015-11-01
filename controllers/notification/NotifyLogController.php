<?hh

class NotifyLogController extends BaseController {
  public static function getPath(): string {
    return '/notify/log';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Member
        });
    $newConfig->setTitle('Notify Log');
    return $newConfig;
  }

  public static function get(): :xhp {
    // Loop through all notification logs
    $content = <div class="col-md-12" />;
    $query = DB::query("SELECT * FROM notify_log ORDER BY id DESC");

    if(empty($query)) {
      return
      <h1>No notification logs found</h1>;
    }

    foreach($query as $row) {
      $content->appendChild(
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">{$row['notify_title'] . ' - ' . $row['sent_time']}</h1>
          </div>
          <div class="panel-body">
            {Email::getXhpMessage($row['notify_text'])}
          </div>
        </div>
      );
    }


    return $content;
  }
}
