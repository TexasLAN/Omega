<?hh

class SuggestionBoxController extends BaseController {
  public static function getPath(): string {
    return '/suggestion';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setTitle('Review');
    return $newConfig;
  }

  private static function validateActions($user): bool {
    return $user->validateRole(UserRoleEnum::Officer) || $user->validateRole(UserRoleEnum::Admin);
  }

  public static function get(): :xhp {

    return
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Send Suggestion</h1>
          </div>
          <div class="panel-body">
            <form method="post" action={self::getPath()}>
              <div class="form-group">
                <textarea class="form-control" rows={3} name="message"></textarea>
              </div>
              <button name="add_suggestion" type="submit" class="btn btn-default">Send</button>
            </form>
          </div>
        </div>
        {self::getSuggestions()}
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getSuggestions(): ?:xhp {
    $user = Session::getUser();

    // Open Suggestions
    $openSuggestions = Suggestions::loadByStatus(SuggestionStatus::Open);
    $openList = <ul class="list-group" />;
    foreach($openSuggestions as $suggestion) {
      $openList->appendChild(
        <li class="list-group-item">
          {$suggestion->getMessageXHP()}
          {self::validateActions($user) ?
            <form class="btn-toolbar" method="post" action={self::getPath()}>
              <button name="suggestion_state" class="btn btn-primary" value={(string) $suggestion->getID()} type="submit">
                {$suggestion->getStatus() == SuggestionStatus::Open ? 'Close' : 'Open'}
              </button>
            </form>
            : <div />
          }
        </li>
      );
    }

    // Closed Suggestions
    $closedSuggestions = Suggestions::loadByStatus(SuggestionStatus::Closed);
    $closedList = <ul class="list-group" />;
    foreach($closedSuggestions as $suggestion) {
      $closedList->appendChild(
        <li class="list-group-item">
          {$suggestion->getMessageXHP()}
          {self::validateActions($user) ?
            <form class="btn-toolbar" method="post" action={self::getPath()}>
              <button name="suggestion_state" class="btn btn-primary" value={(string) $suggestion->getID()} type="submit">
                {$suggestion->getStatus() == SuggestionStatus::Open ? 'Close' : 'Open'}
              </button>
            </form>
            : <div />
          }
        </li>
      );
    }

    return
      <div class="panel panel-default" role="tabpanel">
        <div class="panel-heading">
          <ul class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" class="active">
              <a href="#openList" aria-controls="home" role="tab" data-toggle="tab">Open</a>
            </li>
            <li role="presentation">
              <a href="#closedList" aria-controls="profile" role="tab" data-toggle="tab">Closed</a>
            </li>
          </ul>
        </div>
        <div class="tab-content">
          <br/>
          <div role="tabpanel" class="tab-pane active" id="openList">{$openList}</div>
          <div role="tabpanel" class="tab-pane" id="closedList">{$closedList}</div>
        </div>
      </div>;
  }

  public static function post(): void {
    if(isset($_POST['add_suggestion'])) {
      // Input Validatation
      if(!isset($_POST['message'] || $_POST['message'] == '')) {
        Flash::set('error', 'Message field must be filled out');
        Route::redirect(self::getPath());
      }

      SuggestionsMutator::create()
       ->setMessage($_POST['message'])
       ->setStatus(SuggestionStatus::Open)
       ->save();

      Flash::set('success', 'Suggestion has been created!');
    } elseif(isset($_POST['suggestion_state'])) {
      $suggestion = Suggestions::load((int) $_POST['suggestion_state']);
      if(is_null($suggestion)) {
        Flash::set('error', 'Suggestion state change failed!');
        Route::redirect(self::getPath());
        return;
      }

      SuggestionsMutator::update($suggestion->getID())
       ->setStatus(($suggestion->getStatus() == SuggestionStatus::Open) ? 
                      SuggestionStatus::Closed : SuggestionStatus::Open)
       ->save();

       Flash::set('success', 'Suggestion state changed!');
    }

    Route::redirect(self::getPath());
  }
}
