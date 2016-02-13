<?hh //decl

class FeedbackSingleController extends BaseController {
  public static function getPath(): string {
    return '/feedback/(?<id>\d+)';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setTitle('Feedback');
    return $newConfig;
  }

  public static function get(): :xhp {
    $user_id = (int)$_SESSION['route_params']['id'];
    $user = User::load($user_id);
    if(is_null($user) || !$user->isReviewable()) {
      return FourOhFourController::get();
    }

    $email_hash = md5(strtolower(trim($user->getEmail())));
    $gravatar_url = 'https://secure.gravatar.com/avatar/' . $email_hash . '?s=200';

    $feedback = Feedback::loadByUserAndReviewer($user_id, Session::getUser()->getID());

    return
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1>{$user->getFullName()}</h1>
          </div>
          <div class="panel-body">
            <p class="text-center">
              <img src={$gravatar_url} class="img-thumbnail" />
            </p>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Review</h1>
          </div>
          <div class="panel-body">
            <form method="post" action={'/feedback/' . $user_id}>
              <div class="form-group">
                <label for="review" class="control-label">Comments</label>
                <textarea class="fixed-textarea form-control" rows={5} id="feedback" name="feedback">
                  {(!is_null($feedback)) ? $feedback->getComments() : ''}
                </textarea>
              </div>
              <button type="submit" name="id" value={(string) $user_id} class="btn btn-default">Submit</button>
            </form>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    # Upsert the review
    FeedbackMutator::upsert(
      $_POST['feedback'],
      (int)$_POST['id'],
      Session::getUser()->getID()
    );

    Flash::set('success', 'Feedback submitted!');
    Route::redirect('/feedback/' . $_POST['id']);
  }
}
