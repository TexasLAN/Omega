<?hh

class VoteCandidateController extends BaseController {
  public static function getPath(): string {
    return '/votecandidate';
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

  	if(Settings::getVotingStatus() != VotingStatus::Voting || Session::getUser()->getHasVoted()) {
      return
      <h1>Voting is closed</h1>;
    }

    $main = <form class="btn-toolbar" method="post" action={self::getPath()} />;


    foreach(VoteRoleEnum::getValues() as $name => $roleValue) {
      $roleTitle = <div><h3>{VoteRole::getRoleName(VoteRoleEnum::assert($roleValue))}</h3></div>;
      $main->appendChild($roleTitle);

      $roleCandidates = VoteCandidate::loadRoleByScore(VoteRoleEnum::assert($roleValue));
      $main->appendChild(<input type="hidden" name={(string)$roleValue} value={(string) count($roleCandidates)} />);
      if(count($roleCandidates) == 0) {
        $main->appendChild(<h5>No applicants</h5>);
      }
      for($i = 0; $i < count($roleCandidates); $i++) {
        $select_cand = <select name={$roleValue . '-' . $i}></select>;
        $select_cand->appendChild(<option value={'0'}>{'Null'}</option>);
        foreach($roleCandidates as $candidate) {
          $user = User::load($candidate->getUserID());
          if(is_null($user)) continue;

          $select_cand->appendChild(<option value={(string) $user->getID()}>{$user->getFullName()}</option>);
        }
        $main->appendChild($select_cand);
      }
    }
    $main->appendChild(<br/>);
    $main->appendChild(<br/>);
    $main->appendChild(
      <button
        name="vote_submit"
        type="submit"
        class="btn btn-primary">
        Submit Vote
      </button>);

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
    if(isset($_POST['vote_submit']) && !Session::getUser()->getHasVoted()) {
      // Validate user input
      $isValid = true;
      foreach(VoteRoleEnum::getValues() as $name => $roleValue) {
        $candidate_amt = (int) $_POST[(string) $roleValue];
        $candidate_user_array = array();
        // Put all the values into an array (if there is a non zero after a zero make it invalid)

        for($i = 0; $i < $candidate_amt; $i++) {
          array_push($candidate_user_array, (int) $_POST[($roleValue . '-' . $i)]);
          // Has a null vote then people past that
          if($i > 0 && $candidate_user_array[$i - 1] == 0 && $candidate_user_array[$i] != 0) {
            $isValid = false;
            break;
          }

          // Check for duplicates
          for($j = 0; $j < (count($candidate_user_array) - 1); $j++) {
            if($candidate_user_array[$j] == $candidate_user_array[$i]) {
              $isValid = false;
              break;
            }
          }
        }

        if(!$isValid) break;
      }

      if(!$isValid) {
        Flash::set('error', 'Your vote inputs are invalid!');
        Route::redirect(self::getPath());
      } else {
        // set User has voted and update candidate's scores
        UserMutator::update(Session::getUser()->getID())
          ->setHasVoted(true)
          ->save();
        $ballot_list = array();
        foreach(VoteRoleEnum::getValues() as $name => $roleValue) {
          $candidate_amt = (int) $_POST[(string) $roleValue];
          $candidate_list = array();
          for($i = 0; $i < $candidate_amt; $i++) {
            $candidate_user_id = (int) $_POST[($roleValue . '-' . $i)];
            if($candidate_user_id == 0) break;
            array_push($candidate_list, $candidate_user_id);
          }
          array_push($ballot_list, $candidate_list);
        }
        VoteBallotMutator::create()
          ->setVotingID(Settings::getVotingID())
          ->setVoteList(json_encode($ballot_list))
          ->save();
        Flash::set('success', 'Your vote has been submitted!');
      }
    } else {
      Flash::set('error', 'You have already voted!');
    }
    Route::redirect(VoteController::getPath());
  }
}
