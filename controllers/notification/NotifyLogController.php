<?hh

class NotifyLogController extends BaseController {
  public static function getPath(): string {
    return '/notify/log';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Active});
    $newConfig->setTitle('Notify Log');
    return $newConfig;
  }

  public static function get(): :xhp {
    // Loop through all notification logs
    $content = <div class="col-md-12" />;
    $notifyLogList = NotifyLog::loadAllDesc();

    if (empty($notifyLogList)) {
      return <h1>No notification logs found</h1>;
    }

    foreach ($notifyLogList as $row_log) {
      $content->appendChild(
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">
              {$row_log->getNotifyTitle().
              ' - '.
              ($row_log->getSentTime()->format("Y-m-d H:i"))}
            </h1>
          </div>
          <div class="panel-body">
            {Email::getXhpMessage(
              $row_log->getNotifyText(),
              $row_log->getHtmlParsed()
            )}
          </div>
        </div>
      );
    }

    return $content;
  }
}
