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
    $newConfig->setTitle('Vote Setup');
    return $newConfig;
  }

  private static function validateActions($user): bool {
    return $user->validateRole(UserRoleEnum::Admin);
  }

  public static function get(): :xhp {

  	if(!Settings::get('voting_open')) {
      return
      <h1>Voting is closed</h1>;
    }

    $user = Session::getUser();

    // Generate a table of all the actions for the event list controller
    $admin_action_panel = <div/>;
    if(self::validateActions($user)) {
      $admin_form = <form class="btn-toolbar" method="post" action={self::getPath()} />;
      $no_candidates = VoteCandidate::countCandidates() == 0;
      if(!Settings::get('voting_in_progress')) {
        if($no_candidates) {
          // Voting hasnt started
          $admin_form->appendChild(
            <button
              name="start_voting"
              type="submit"
              class="btn btn-primary">
              Start Voting
            </button>);
        } else {
          // Voting has ended
          $admin_form->appendChild(
            <button
              name="close_voting"
              type="submit"
              class="btn btn-primary">
              Close Voting
            </button>);
        }
      } else {
        $admin_form->appendChild(
          <button
            name="end_voting"
            type="submit"
            class="btn btn-primary">
            End Voting
          </button>);
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

    $action_panel = <div/>;
    $voting_dialog = <div/>;
    if(Settings::get('voting_in_progress')) {
      $voting_dialog->appendChild(self::getVotingModal());
      $form = <form class="btn-toolbar" method="post" action={self::getPath()} />;
      $form->appendChild(
        <button
          name="vote"
          type="submit"
          class="btn btn-primary">
          Vote
        </button>);
      $action_panel = 
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Actions</h1>
          </div>
          <div class="panel-body">
            {$form}
          </div>
        </div>;
    }


    // Show candidates
    $main = <div/>;

    foreach(VoteRoleEnum::getValues() as $name => $value) {
      $roleMain = <div><h3>{VoteRole::getRoleName(VoteRoleEnum::assert($value))}</h3></div>;

      $roleCandidates = VoteCandidate::loadRoleByScore(VoteRoleEnum::assert($value));
      if (count($roleCandidates) == 0) {
        $roleMain->appendChild(<h5>No applicants yet</h5>);
      }
      foreach($roleCandidates as $candidate) {
        $user = User::load($candidate->getUserID());
        if(is_null($user)) continue;

        $roleMain->appendChild(<a href={VoteApplicationProfileController::getPrePath() . $value . '/' . $user->getID()}><h5>{$user->getFullName()} - Score: {$candidate->getScore()}</h5></a>);
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

  private static function getVotingModal(): :xhp {
    return
      <div class="modal fade" id="eventMutator" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="eventName" />
            </div>
            <div class="modal-body">
              <form action={self::getPath()} method="post">
                <div>
                  <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" id="name" />
                  </div>
                  <div class="form-group">
                    <label>Location</label>
                    <input type="text" class="form-control" name="location" id="location" />
                  </div>
                  <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" />
                  </div>
                  <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" class="form-control" name="start_time" id="start_time" />
                  </div>
                  <div class="form-group">
                    <label>End Date</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" />
                  </div>
                  <div class="form-group">
                    <label>End Time</label>
                    <input type="time" class="form-control" name="end_time" id="end_time" />
                  </div>
                  <div class="form-group">
                    <label>Description</label>
                  </div>
                  <div class="form-group">
                    <textarea class="fixed-textarea" name="description" id="description" />
                  </div>
                </div>
                <input type="hidden" name="event_mutator" />
                <input type="hidden" name="method" id="method"/>
                <input type="hidden" name="id" id="id"/>
                <input type="hidden" name="type" id="type"/>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="submit">Save</button>
            </div>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    if(isset($_POST['start_voting'])) {
      // Verify there is enough applicants
      foreach(VoteRoleEnum::getValues() as $name => $value) {
        $roleCandidates = VoteCandidate::loadRole(VoteRoleEnum::assert($value));
        if (count($roleCandidates) == 0) {
          Flash::set('error', 'Not all positions have been applied for!');
          Route::redirect(self::getPath());
        }
      }

      Settings::set('voting_in_progress', 1);
    }
    Route::redirect(self::getPath());
  }
}
