<?hh

class VoteApplicationController extends BaseController {
  public static function getPrePath(): string {
    return '/voteapply/';
  }
  public static function getPath(): string {
    return self::getPrePath() . '(?<id>\d+)';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setTitle('Vote Application');
    return $newConfig;
  }

  public static function get(): :xhp {
    $vote_role_id = (int)$_SESSION['route_params']['id'];

  	if(!Settings::get('voting_open')) {
      return
      <h1>Voting is closed</h1>;
    }
    if(Settings::get('voting_in_progress')) {
      return
      <div>
      	<h1>Voting is in progress, one can not apply at this time.</h1>
      </div>;
    }
    if(!is_null(VoteCandidate::loadByRoleAndUser($vote_role_id, Session::getUser()->getID()))) {
      return
      <div>
        <h1>Already submitted an application.</h1>
      </div>;
    }

    return
      <x:frag>
        <div class="panel panel-default">
          <div class="panel-body">
            <h1>Applying for {VoteRole::getRoleName($vote_role_id)}</h1>
            <form action={self::getPrePath() . $vote_role_id} method="post">
              <div class="form-group">
                <label for="description" class="control-label">
                  Describe why you should be in this role
                </label>
                <textarea class="form-control" rows={3} id="description" name="description">
                </textarea>
              </div>
              <button
                name="submit"
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

  }
}
