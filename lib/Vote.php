<?hh //decl

class Vote {

	public static function tally(): bool {
		// Setup ballots
		$ballot_list = self::getBallotList();

		$result_map = self::getPreviousResults();

		$unfinished_voting = false;

		// Find winner of each role
		foreach(VoteRoleEnum::getValues() as $roleName => $roleValue) {
			// Stop this role if already in results
			foreach($result_map as $result_user_id => $result_role_id) {
				if($roleValue == $result_role_id) continue;
			}

			$candidate_map = self::getValidCandidates($result_map);

			$winner_id = self::getWinnerUserIdForRole($candidate_map, $ballot_list);

			// If winner found, add to result map
			if($winner_id != 0) {
				$result_map[$winner_id] = $roleValue;
			} elseif (VoteRole::isVotingPosition($roleValue)) {
				// Winner not found and is a voting position
				// Break the voting and prompt admin to redo voting system
				$unfinished_voting = true;
				break;
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

		return $unfinished_voting;
	}

	private static function getBallotList(): array {
		$result = array();
		foreach(VoteBallot::loadBallots() as $ballot) {
			array_push($result, $ballot->_getVoteList());
		}
		return $result;
	}

	private static function getPreviousResults(): array {
		$result = array();
		// See if anyone has won the current election spots (in case of a revote)
		foreach(VoteRoleEnum::getValues() as $roleName => $roleValue) {
			$winning_cand = VoteCandidate::loadWinnerByRole($roleValue);
			if(is_null($winning_cand)) {
				break;
			} else {
				$result[$winning_cand->getUserID()] = $roleValue;
			}
		}
		return $result;
	}

	private static function getValidCandidates(array $result_map): array {
		$candidate_map = array();

		// Put candidate into hashmap (do not include those who already have a position)
		foreach(VoteCandidate::loadRole(VoteRoleEnum::assert($roleValue)) as $candidate) {
			if(!isset($result_map[$candidate->getUserID()])) {
				$candidate_map[$candidate->getUserID()] = 1;
			}
		}

		return $candidate_map;
	}

	private static function getWinnerUserIdForRole(array &$candidate_map, array $ballot_list): int {
		$winner_id = 0;
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
			// TODO 
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
			}
		}
		return $winner_id;
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