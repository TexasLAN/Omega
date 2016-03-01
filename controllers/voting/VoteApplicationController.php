<?hh

class VoteApplicationController extends BaseController {
  public static function getPrePath(): string {
    return '/voteapply/';
  }
  public static function getPath(): string {
    return self::getPrePath().'(?<id>\d+)';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Active});
    $newConfig->setTitle('Vote Application');
    return $newConfig;
  }

  public static function get(): :xhp {
    $vote_role_id = (int) $_SESSION['route_params']['id'];

    if (Settings::getVotingStatus() != VotingStatus::Apply) {
      return <h1>Election applications are closed</h1>;
    }
    if (!is_null(
          VoteCandidate::loadByRoleAndUser(
            $vote_role_id,
            Session::getUser()->getID(),
          ),
        )) {
      return
        <div>
          <h1>Already submitted an application.</h1>
        </div>;
    }
    if(!is_null(VoteCandidate::loadWinnerByRole($vote_role_id))) {
      return
        <div>
          <h1>Election applications are closed for this role.</h1>
        </div>;
    }

    return
      <x:frag>
        <div class="panel panel-default">
          <div class="panel-body">
            <h1>Applying for {VoteRole::getRoleName($vote_role_id)}</h1>
            <form action={self::getPrePath().$vote_role_id} method="post">
              <div class="form-group">
                <label for="description" class="control-label">
                  Describe why you should be in this role
                </label>
                <textarea
                  class="form-control"
                  rows={3}
                  id="description"
                  name="description">
                </textarea>
              </div>
              <button
                name="create_vote_app"
                type="submit"
                class="btn btn-primary">
                Submit
              </button>
            </form>
          </div>
        </div>
      </x:frag>;
  }

  public static function post(): void {
    $vote_role_id = (int) $_SESSION['route_params']['id'];
    if (isset($_POST['create_vote_app'])) {
      VoteCandidateMutator::create()
        ->setVoteRole($vote_role_id)
        ->setUserID(Session::getUser()->getID())
        ->setDescription($_POST['description'])
        ->setScore(0)
        ->setVotingID(Settings::getVotingID())
        ->save();

      Route::redirect(
        VoteApplicationProfileController::getPrePath().
        $vote_role_id.
        '/'.
        Session::getUser()->getID(),
      );
    }

    Route::redirect(VoteApplyController::getPath());
  }
}
