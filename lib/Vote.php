<?hh //decl

class Vote {

	/* 
	 * Runs the whole election cycle
	 * Returns true if the election is finished
	 */
	public static function tally(): bool {
		// Setup ballots
		$ballot_list = self::getBallotList();
		// List of those who have won <UserId, RoleValueWon>
		$result_map = self::getPreviousResults();

		$voting_finished = true;

		// Find winner of each role
		foreach(VoteRoleEnum::getValues() as $roleName => $roleValue) {
			error_log('VoteRole = ' . $roleName);
			error_log('num of won candidates = ' . self::getCountOfRoleWinners($result_map, $roleValue));
			error_log('needed candidates = ' . VoteRole::getAmtOfPositions($roleValue));

			while(self::getCountOfRoleWinners($result_map, $roleValue) < VoteRole::getAmtOfPositions($roleValue)) {
				$winner_id = self::getWinnerUserIdForRole($result_map, $ballot_list, $roleValue);
				error_log('Winner_id for ' . $roleName . ' = ' . $winner_id);
				// If winner found, add to result map
				if($winner_id != 0) {
					$result_map[$winner_id] = $roleValue;
				} else {
					if(VoteRole::isVotingPosition($roleValue)) {
						// Winner not found and is a voting position
						// Break the voting and prompt admin to redo voting system
						$voting_finished = false;
					}
					break;
				}
				error_log('num of won candidates = ' . self::getCountOfRoleWinners($result_map, $roleValue));
				error_log('needed candidates = ' . VoteRole::getAmtOfPositions($roleValue));
			}

			if(!$voting_finished) {
				// Error found with finding Winner, reelection needed
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

		return $voting_finished;
	}

	/* 
	 * Gets the vote ballots that the users submitted
	 */
	private static function getBallotList(): array {
		$result = array();
		foreach(VoteBallot::loadBallots() as $ballot) {
			array_push($result, $ballot->_getVoteList());
		}
		return $result;
	}

	// Gets the previous results that were valid
	private static function getPreviousResults(): array {
		$result = array();
		// See if anyone has won the current election spots (in case of a revote)
		foreach(VoteRoleEnum::getValues() as $roleName => $roleValue) {
			$winning_cand_array = VoteCandidate::loadWinnersByRole($roleValue);

			// Add all previous winners
			foreach($winning_cand_array as $row_cand) {
				$result[$row_cand->getUserID()] = $roleValue;
			}

			// Stop if one position isnt full
			if(count($winning_cand_array) < VoteRole::getAmtOfPositions($roleValue)) {
				break;
			}
		}
		return $result;
	}

	private static function getCountOfRoleWinners(array $result_map, int $roleValue): int {
		$result = 0;

		foreach($result_map as $candId => $candRole) {
			if($candRole == $roleValue) {
				$result++;
			}
		}

		return $result;
	}

	/* 
	 * Get the remaining valid candidates
	 */
	private static function getValidCandidates(array $result_map, int $roleValue): Map<int, bool> {
		$candidate_map = Map<int, bool> {};

		// Put candidate into hashmap (do not include those who already have a position)
		foreach(VoteCandidate::loadRole(VoteRoleEnum::assert($roleValue)) as $candidate) {
			if(!isset($result_map[$candidate->getUserID()])) {
				$candidate_map->set($candidate->getUserID(), true);
			}
		}

		return $candidate_map;
	}

	/* 
	 * Gets the winner user id for a role depending on the ballots
	 * Returns 0 if there was no winner found.
	 */
	private static function getWinnerUserIdForRole(array $result_map, array $ballot_list, int $roleValue,): int {
		$candidate_map = self::getValidCandidates($result_map, $roleValue);
		$winner_id = 0;
		// Find winner for this role
		// error_log('---finding winner');
		while($winner_id == 0 && $candidate_map->count() > 0) {
			// error_log('---WHILE!');

			$candidate_votes_list = array();

			// Count voters first choice
			foreach($ballot_list as $ballot) {
				$role_ballot = $ballot[$roleValue];
				$first_place_id = 0;

				// Find first place
				for($i = 0; $i < count($role_ballot); $i++) {
					if($candidate_map->contains($role_ballot[$i])) {
						$first_place_id = $role_ballot[$i];
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

			$majority = self::getMajorityCount();
			$bigIdList = array();
			$smallIdList = array();

			if(count($candidate_votes_list) == 0) {
				// Voting is invalid
				return 0;
			}

			// Find minorities to remove to progress for tie or non-majority
			asort($candidate_votes_list);
			$smallestValue = $candidate_votes_list[array_keys($candidate_votes_list)[0]];
			foreach($candidate_votes_list as $candidate_user_id => $candidate_vote) {
				if($candidate_vote < $majority && (count($smallIdList) == 0) || $smallestValue == $candidate_vote ) {
					array_push($smallIdList, $candidate_user_id);
				}
			}

			// Find majorities to check winner
			arsort($candidate_votes_list);
			$biggestValue = $candidate_votes_list[array_keys($candidate_votes_list)[0]];
			foreach($candidate_votes_list as $candidate_user_id => $candidate_vote) {
				// error_log('---compare for biggest = vote = ' . $candidate_vote . ' majority = ' . $majority);
				if($candidate_vote >= $majority && (count($bigIdList) == 0) || $biggestValue == $candidate_vote ) {
					array_push($bigIdList, $candidate_user_id);
				}
			}
			// error_log(json_encode($smallIdList));
			// error_log(json_encode($bigIdList));

			// Handle cases (no winner, winner, tie)
			if(count($bigIdList) == 0) {
				// error_log('---status ==== no winner');
				// Remove minorities
				foreach($smallIdList as $i => $candUserId) {
					// error_log('---removing the user = ' . $candUserId);
					$candidate_map->remove($candUserId);
				}
			} elseif(count($bigIdList) == 1) {
				// error_log('---status ==== winner winner');
				$winner_id = $bigIdList[0];
			} else {
				// error_log('---status ==== tie');
				// Check if there is any minorities
				if(count($smallIdList) > 0) {
					foreach($smallIdList as $i => $candUserId) {
						// error_log('---removing the user = ' . $candUserId);
						$candidate_map->remove($candUserId);
					}
				} else {
					// Voting is invalid
					return 0;
				}
			}
		}
		return $winner_id;
	}

	public static function getMajorityCount(): int {
		$majority = (int) ceil((float) count(User::loadStates(Vector {UserState::Active})) / 2.0);
		return $majority;
	}

	/* 
	 * Sets up the voting system for a reelection
	 */
	public static function redoElection(): void {
		// Invalidate all the ballots of this election
		foreach(VoteBallot::loadBallots() as $ballot) {
			VoteBallotMutator::update($ballot->getID())
				->setValid(false)
				->save();
		}
		// Reset users vote to hasnt vote
		foreach(User::loadHasVoted() as $user) {
			UserMutator::update($user->getID())
				->setHasVoted(false)
				->save();
		}

		Settings::set('voting_status', VotingStatus::Apply);
	}


	/* 
	 * Closes the current election up and moves it to the next one for next cycle.
	 */
	public static function closeVoting(): void {
		Settings::set('voting_id', Settings::getVotingID() + 1);
		$userList = User::loadForAutoComplete();
		foreach($userList as $row) {
			UserMutator::update($row->getID())
				->setHasVoted(0)
				->save();
		}
	}
}
