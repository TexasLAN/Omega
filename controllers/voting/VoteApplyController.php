<?hh

class VoteApplyController extends BaseController {
  public static function getPath(): string {
    return '/voteapply';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Active});
    $newConfig->setTitle('Vote Apply');
    return $newConfig;
  }

  public static function get(): :xhp {

    if (Settings::getVotingStatus() != VotingStatus::Apply) {
      return <h1>Election applications are closed</h1>;
    }

    $main = <div />;

    foreach (VoteRoleEnum::getValues() as $name => $value) {
      $roleMain =
        <div>
          <a
            href={VoteApplicationController::getPrePath().$value}
            class="btn btn-primary">
            Apply
          </a>
          <h3 style="display:inline-block;">
            &emsp;{VoteRole::getRoleName(VoteRoleEnum::assert($value))}
          </h3>
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
