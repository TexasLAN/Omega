<?hh

class VoteController extends BaseController {
  public static function getPath(): string {
    return '/vote';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Active});
    $newConfig->setTitle('Vote Setup');
    return $newConfig;
  }

  private static function validateActions($user): bool {
    return $user->validateRole(UserRoleEnum::Admin);
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    if (Settings::getVotingStatus() == VotingStatus::Closed) {
      return <h1>Voting is closed</h1>;
    }

    // Generate a table of all the actions for the event list controller
    $admin_action_panel = <div />;
    if (self::validateActions($user)) {
      $admin_form =
        <form class="btn-toolbar" method="post" action={self::getPath()} />;
      $admin_form->appendChild(
        <p>Vote Count: {count(VoteBallot::loadBallots())} / {count(User::loadStates(Vector {UserState::Active}))}</p>
      );
      switch (Settings::getVotingStatus()) {
        case VotingStatus::Closed:
          $admin_form->appendChild(
            <button
              name="open_voting"
              type="submit"
              class="btn btn-primary">
              Open Voting
            </button>,
          );
          break;
        case VotingStatus::Apply:
          $admin_form->appendChild(
            <button
              name="start_voting"
              type="submit"
              class="btn btn-primary">
              Start Voting
            </button>,
          );
          break;
        case VotingStatus::Voting:
          $admin_form->appendChild(
            <button
              name="stop_voting"
              type="submit"
              class="btn btn-primary">
              Stop Voting
            </button>,
          );
          break;
        case VotingStatus::Results:
          $admin_form->appendChild(
            <button
              name="close_voting"
              type="submit"
              class="btn btn-primary">
              Close Voting
            </button>,
          );
          break;
      }

      $admin_action_panel =
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Admin Actions</h1>
          </div>
          <div class="panel-body">
            {$admin_form}
          </div>
        </div>;
    }

    $action_panel = <div />;
    if (Settings::getVotingStatus() == VotingStatus::Voting &&
        !Session::getUser()->getHasVoted()) {
      $action_panel =
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Actions</h1>
          </div>
          <div class="panel-body">
            <a
              href={VoteCandidateController::getPath()}
              class="btn btn-primary">
              Vote
            </a>
          </div>
        </div>;
    }

    // Show candidates
    $main = <div />;

    foreach (VoteRoleEnum::getValues() as $name => $value) {
      $roleMain =
        <div>
          <h3>{VoteRole::getRoleName(VoteRoleEnum::assert($value))}</h3>
        </div>;

      $roleCandidates =
        VoteCandidate::loadRoleByScore(VoteRoleEnum::assert($value));
      if (count($roleCandidates) == 0) {
        $roleMain->appendChild(<h5>No applicants yet</h5>);
      }
      foreach ($roleCandidates as $candidate) {
        $user = User::load($candidate->getUserID());
        if (is_null($user))
          continue;

        $roleMain->appendChild(
          <a
            href=
              {VoteApplicationProfileController::getPrePath().
              $value.
              '/'.
              $user->getID()}>
            <h5>
              {$user->getFullName()}
              {($candidate->getScore() == 1) ? ' - Won Position' : ''}
              {(VoteRoleEnum::StandardsBoard == $value && 
                Settings::getVotingStatus() == VotingStatus::Closed) ?
                 '' : ''}
            </h5>
          </a>,
        );
      }
      $main->appendChild($roleMain);
    }

    return
      <x:frag>
        {$admin_action_panel}
        {$action_panel}
        <div class="panel panel-default">
          <div class="panel-body">
            {$main}
          </div>
        </div>
      </x:frag>;
  }

  public static function post(): void {
    if (isset($_POST['open_voting'])) {
      Settings::set('voting_status', VotingStatus::Apply);
    } else if (isset($_POST['start_voting'])) {
      // Verify there is enough applicants
      foreach (VoteRoleEnum::getValues() as $name => $value) {
        $roleCandidates =
          VoteCandidate::loadRole(VoteRoleEnum::assert($value));
        if (count($roleCandidates) == 0 &&
            VoteRole::isVotingPosition($value)) {
          Flash::set(
            'error',
            'Not all voting positions have been applied for!',
          );
          Route::redirect(self::getPath());
        }
      }

      Settings::set('voting_status', VotingStatus::Voting);
    } else if (isset($_POST['stop_voting'])) {
      if(Vote::getMajorityCount() > count(VoteBallot::loadBallots())) {
        Flash::set(
            'error',
            'Not enough people have voted!',
          );
          Route::redirect(self::getPath());
      }
      Settings::set('voting_status', VotingStatus::Results);
      $voting_finished = Vote::tally();
      if (!$voting_finished) {
        Settings::set('voting_status', VotingStatus::Apply);
        Vote::redoElection();
        Flash::set(
            'error',
            'There is an issue with the Election, a reelection is needed!',
          );
      }
    } else if (isset($_POST['close_voting'])) {
      Settings::set('voting_status', VotingStatus::Closed);
      Vote::closeVoting();
    }
    Route::redirect(self::getPath());
  }
}
