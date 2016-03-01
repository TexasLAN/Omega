<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<0d34006de527bcf2086282f74c4f3e9d>>
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

  public function getValid(): bool {
    return (bool) $this->data['valid'];
  }

  /* BEGIN MANUAL SECTION VoteBallot_footer */
  // Insert additional methods here
  public function getID(): int {
    return (int) $this->data['id'];
  }

  public function _getVoteList(): array {
    $decoded_json = json_decode((string) $this->data['vote_list']);
    if (is_null($decoded_json)) {
      return array();
    } else {
      return $decoded_json;
    }
  }

  public static function loadBallots(): array<VoteCandidate> {
    $query = DB::query(
      'SELECT * FROM vote_ballot WHERE valid=1 AND voting_id='.Settings::getVotingID(),
    );
    if (!$query) {
      return array();
    }
    return array_map(
      function($value) {
        return new VoteBallot(new Map($value));
      },
      $query,
    );
  }
  /* END MANUAL SECTION */
}
