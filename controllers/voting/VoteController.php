<?hh

class VoteController extends BaseController {
  public static function getPath(): string {
    return '/vote';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setTitle('Vote');
    return $newConfig;
  }

  public static function get(): :xhp {
  	if(!Settings::get('voting_open')) {
      return
      <h1>Voting is closed</h1>;
    }
    if(!Settings::get('voting_in_progress')) {
      return
      <div>
      	<h1>Voting is not progress, one can not vote at this time.</h1>
      </div>;
    }

    // Show candidates

    $main = <div/>;

    foreach(VoteRoleEnum::getValues() as $name => $value) {
      $roleMain = <div><h3>{VoteRole::getRoleName(VoteRoleEnum::assert($value))}</h3></div>;

      $roleCandidates = VoteCandidate::loadRoleByScore(VoteRoleEnum::assert($value));
      if (count($roleCandidates) == 0) {
        $roleMain->appendChild(<h5>&emsp;No applicants yet</h5>);
      }
      foreach($roleCandidates as $candidate) {
        $user = User::load($candidate->getUserID());
        if(is_null($user)) continue;

        $roleMain->appendChild(<h5>&emsp;{$user->getFullName()} - Score: {$candidate->getScore()}</h5>);
      }
      $main->appendChild($roleMain);
    }

    return
      <x:frag>
        <div class="panel panel-default">
          <div class="panel-body">
            {$main}
          </div>
        </div>
      </x:frag>;
  }

  public static function post(): void {

  }
}
