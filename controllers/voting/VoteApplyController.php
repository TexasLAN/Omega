<?hh

class VoteApplyController extends BaseController {
  public static function getPath(): string {
    return '/voteapply';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setTitle('Vote Apply');
    return $newConfig;
  }

  public static function get(): :xhp {

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

    $main = <div/>;

    foreach(VoteRoleEnum::getValues() as $name => $value) {
      $roleMain = 
      <div>
        <a href={VoteApplicationController::getPrePath() . $value} class="btn btn-primary">
           Apply
        </a>
        <h3 style="display:inline-block;">&emsp;{VoteRole::getRoleName(VoteRoleEnum::assert($value))}</h3>
      </div>;

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
}
