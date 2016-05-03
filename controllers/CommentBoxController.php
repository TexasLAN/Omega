<?hh

class CommentBoxController extends BaseController {
  public static function getPath(): string {
    return '/comment';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Active});
    $newConfig->setTitle('Comment Box');
    return $newConfig;
  }

  private static function validateActions($user): bool {
    return
      $user->validateRole(UserRoleEnum::Officer) ||
      $user->validateRole(UserRoleEnum::Admin);
  }

  public static function get(): :xhp {

    return
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Send Comment</h1>
          </div>
          <div class="panel-body">
            <form method="post" action={self::getPath()}>
              <div class="form-group">
                <textarea class="form-control" rows={3} name="message">
                </textarea>
              </div>
              <button
                name="add_comment"
                type="submit"
                class="btn btn-default">
                Send
              </button>
            </form>
          </div>
        </div>
        {self::getComments()}
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getComments(): ?:xhp {
    $user = Session::getUser();

    // Open Comments
    $openSuggestions = Comment::loadByStatus(CommentStatus::Open);
    $openList = <ul class="list-group" />;
    foreach ($openSuggestions as $suggestion) {
      $openList->appendChild(
        <li class="list-group-item">
          {$suggestion->getMessageXHP()}
          {self::validateActions($user)
            ?
            <form
              class="btn-toolbar"
              method="post"
              action={self::getPath()}>
              <button
                name="comment_state"
                class="btn btn-primary"
                value={(string) $suggestion->getID()}
                type="submit">
                {$suggestion->getStatus() == CommentStatus::Open
                  ? 'Close'
                  : 'Open'}
              </button>
            </form>
            : <div />}
        </li>
      );
    }

    // Closed Comments
    $closedSuggestions = Comment::loadByStatus(CommentStatus::Closed);
    $closedList = <ul class="list-group" />;
    foreach ($closedSuggestions as $suggestion) {
      $closedList->appendChild(
        <li class="list-group-item">
          {$suggestion->getMessageXHP()}
          {self::validateActions($user)
            ?
            <form
              class="btn-toolbar"
              method="post"
              action={self::getPath()}>
              <button
                name="comment_state"
                class="btn btn-primary"
                value={(string) $suggestion->getID()}
                type="submit">
                {$suggestion->getStatus() == CommentStatus::Open
                  ? 'Close'
                  : 'Open'}
              </button>
            </form>
            : <div />}
        </li>
      );
    }

    return
      <div class="panel panel-default" role="tabpanel">
        <div class="panel-heading">
          <ul class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" class="active">
              <a
                href="#openList"
                aria-controls="home"
                role="tab"
                data-toggle="tab">
                Open
              </a>
            </li>
            <li role="presentation">
              <a
                href="#closedList"
                aria-controls="profile"
                role="tab"
                data-toggle="tab">
                Closed
              </a>
            </li>
          </ul>
        </div>
        <div class="tab-content">
          <br />
          <div role="tabpanel" class="tab-pane active" id="openList">
            {$openList}
          </div>
          <div role="tabpanel" class="tab-pane" id="closedList">
            {$closedList}
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    if (isset($_POST['add_comment'])) {
      // Input Validatation
      if (!isset($_POST['message'] || $_POST['message'] == '')) {
        Flash::set('error', 'Message field must be filled out');
        Route::redirect(self::getPath());
      }

      CommentMutator::create()
        ->setMessage($_POST['message'])
        ->setStatus(CommentStatus::Open)
        ->save();

      Flash::set('success', 'Comment has been created!');
    } elseif (isset($_POST['comment_state'])) {
      $suggestion = Comment::load((int) $_POST['comment_state']);
      if (is_null($suggestion)) {
        Flash::set('error', 'Comment state change failed!');
        Route::redirect(self::getPath());
        return;
      }

      CommentMutator::update($suggestion->getID())->setStatus(
        ($suggestion->getStatus() == CommentStatus::Open)
          ? CommentStatus::Closed
          : CommentStatus::Open,
      )->save();

      Flash::set('success', 'Comment state changed!');
    }

    Route::redirect(self::getPath());
  }
}
