<?hh

class VoteSetupController extends BaseController {
  public static function getPath(): string {
    return '/votesetup';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setUserRoles(
      Vector {
        UserRoleEnum::Admin
        });
    $newConfig->setTitle('Vote Setup');
    return $newConfig;
  }

  public static function get(): :xhp {

  	if(!Settings::get('voting_open')) {
      return
      <h1>Voting is closed</h1>;
    }
    if(Settings::get('voting_in_progress')) {
      // Show results
      return
      <div>
      	<h1>Voting is in progress, no changes can be made</h1>
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
            <br/>
            <form class="btn-toolbar" method="post" action={self::getPath()}>
              <button
                name="start_voting"
                type="submit"
                class="btn btn-primary">
                Start Voting
              </button>
            </form>
          </div>
        </div>
      </x:frag>;
  }

  public static function post(): void {
    if(isset($_POST['start_voting'])) {
      foreach(VoteRoleEnum::getValues() as $name => $value) {
        $roleCandidates = VoteCandidate::loadRole(VoteRoleEnum::assert($value));
        if (count($roleCandidates) == 0) {
          Flash::set('error', 'Not all positions have been applied for!');
          Route::redirect(self::getPath());
        }
      }
    }
    Route::redirect(self::getPath());
  }
}
