<?hh //decl

class Vote {

	public static function tally() {
		// Setup ballots
		$ballot_list = array();
		foreach(VoteBallot::loadBallots() as $ballot) {
			array_push($ballot_list, $ballot->_getVoteList());
		}

		$result_map = array();

		// Find winner of each role
		foreach(VoteRoleEnum::getValues() as $roleName => $roleValue) {
			$winner_id = 0;
			$candidate_map = array();

			// Put candidate into hashmap (do not include those who already have a position)
			foreach(VoteCandidate::loadRole(VoteRoleEnum::assert($roleValue)) as $candidate) {
				if(!isset($result_map[$candidate->getUserID()])) {
					$candidate_map[$candidate->getUserID()] = 1;
				}
			}

			// Find winner for this role
			while($winner_id == 0 && count($candidate_map) > 0) {
				$candidate_votes_list = array();
				$candidate_count_first_selection_list = array();

				// Count voters first choice
				foreach($ballot_list as $ballot) {
					$role_ballot = $ballot[$roleValue];
					$first_place_id = 0;

					// Find first place
					for($i = 0; $i < count($role_ballot); $i++) {
						if(isset($candidate_map[$role_ballot[$i]])) {
							$first_place_id = $role_ballot[$i];

							// Keep track of counts of actual first choices for ties
							if($i == 0) {
								$candidate_count_first_selection_list[$first_place_id] = 
									(!isset($candidate_count_first_selection_list[$first_place_id])) ? 
										1 :
										$candidate_count_first_selection_list[$first_place_id] + 1;
							}
							break;
						}
					}

					// If first place was found, increment vote counter
					if($first_place_id != 0) {
						$candidate_votes_list[$first_place_id] = 
							(!isset($candidate_votes_list[$first_place_id])) ? 
								1 :
								$candidate_votes_list[$first_place_id] + 1;
					}
				}

				// Find majority winner(s)
				$majority = (int) ceil((float) count($ballot_list) / 2);
				$biggest_id_list = array();

				arsort($candidate_votes_list);
				$last_place_id = end(array_keys($candidate_votes_list));
				reset($candidate_votes_list);
				foreach($candidate_votes_list as $candidate_id => $candidate_vote) {
					if($candidate_vote >= $majority) {
						array_push($biggest_id_list, $candidate_id);
					}
				}

				// Handle cases (no winner, winner, tie)
				if(count($biggest_id_list) == 0) {
					unset($candidate_map[$last_place_id]);
				} elseif(count($biggest_id_list) == 1) {
					$winner_id = $biggest_id_list[0];
				} else {
					if($candidate_count_first_selection_list[$biggest_id_list[0]] < $candidate_count_first_selection_list[$biggest_id_list[1]]) {
						$winner_id = $biggest_id_list[1];
					} else {
						$winner_id = $biggest_id_list[0];
					}
				}
			}

			// If winner found, add to result map
			if($winner_id != 0) {
				$result_map[$winner_id] = $roleValue;
			}
		}

		// Update DB with winners
		foreach($result_map as $user_id => $roleValue) {
			$candidate = VoteCandidate::loadByRoleAndUser($roleValue, $user_id);
			if(!is_null($candidate)) {
				VoteCandidateMutator::update($candidate->getID())
					->setScore(1)
					->save();
			}
		}
	}

	public static function closeVoting() {
		Settings::set('voting_id', Settings::getVotingID() + 1);
		$userList = User::loadForAutoComplete();
		foreach($userList as $row) {
			UserMutator::update($row->getID())
				->setHasVoted(0)
				->save();
		}
	}
}