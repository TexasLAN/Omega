<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<3e2abe1bb76db93714a7eb3f7fa06b01>>
 */

final class VoteBallot {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?VoteBallot {
    $result = DB::queryFirstRow("SELECT * FROM vote_ballot WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new VoteBallot(new Map($result));
  }

  public function getVotingID(): int {
    return (int) $this->data['voting_id'];
  }

  public function getVoteList(): string {
    return (string) $this->data['vote_list'];
  }

  /* BEGIN MANUAL SECTION VoteBallot_footer */
  // Insert additional methods here

  public function _getVoteList(): array {
    $decoded_json = json_decode((string) $this->data['vote_list']);
    if(is_null($decoded_json)) {
      return array();
    } else {
      return $decoded_json;
    }
  }

  public static function loadBallots(): array<VoteCandidate> {
    $query = DB::query('SELECT * FROM vote_ballot WHERE voting_id=' . Settings::getVotingID());
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new VoteBallot(new Map($value));
    }, $query);
  }
  /* END MANUAL SECTION */
}
