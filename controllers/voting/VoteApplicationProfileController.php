<?hh

class VoteApplicationProfileController extends BaseController {
  public static function getPrePath(): string {
    return '/voteapply/';
  }
  public static function getPath(): string {
    return self::getPrePath() . '(?<id>\d+)' . '/' . '(?<user_id>\d+)';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setTitle('Vote Application Profile');
    return $newConfig;
  }

  public static function get(): :xhp {
    $vote_role_id = (int)$_SESSION['route_params']['id'];
    $user_id = (int)$_SESSION['route_params']['user_id'];

  	if(!Settings::get('voting_open')) {
      return
      <h1>Voting is closed</h1>;
    }

    $voteCandidate = VoteCandidate::loadByRoleAndUser($vote_role_id, $user_id);
    $user = User::load($user_id);
    if(is_null($voteCandidate) || is_null($user)) {
      return
      <div>
        <h1>There is no application for this user on this position.</h1>
      </div>;
    }


    return
      <x:frag>
        <div class="panel panel-default">
          <div class="panel-body">
            <h1>Persons application yay</h1>
          </div>
        </div>
      </x:frag>;
  }

  public static function post(): void {

  }
}
