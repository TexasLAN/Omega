<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<4515643cfb7cce08fb6d2b38c18eb873>>
 */

final class VoteCandidate {

  private function __construct(private Map<string, mixed> $data) {}

  public static function load(int $id): ?VoteCandidate {
    $result =
      DB::queryFirstRow("SELECT * FROM vote_candidates WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new VoteCandidate(new Map($result));
  }

  public function getVoteRole(): int {
    return (int) $this->data['vote_role'];
  }

  public function getUserID(): int {
    return (int) $this->data['user_id'];
  }

  public function getScore(): int {
    return (int) $this->data['score'];
  }

  public function getDescription(): string {
    return (string) $this->data['description'];
  }

  public function getVotingID(): int {
    return (int) $this->data['voting_id'];
  }

  /* BEGIN MANUAL SECTION VoteCandidate_footer */
  // Insert additional methods here
  public function getID(): int {
    return (int) $this->data['id'];
  }

  public static function countCandidates(): int {
    $query = DB::query(
      "SELECT * FROM vote_candidates WHERE voting_id=%d",
      Settings::getVotingID(),
    );
    if (!$query) {
      return 0;
    }
    $array = array_map(
      function($value) {
        return new VoteCandidate(new Map($value));
      },
      $query,
    );
    return count($array);
  }

  public static function loadWinnerByRole(int $role_id): ?VoteCandidate {
    $result =
      DB::queryFirstRow(
        "SELECT * FROM vote_candidates WHERE vote_role=%d AND score=1 AND voting_id=%d",
        $role_id,
        Settings::getVotingID(),
      );
    if (!$result) {
      return null;
    }
    return new VoteCandidate(new Map($result));
  }

  public static function loadByRoleAndUser(
    int $role_id,
    int $user_id,
  ): ?VoteCandidate {
    $result =
      DB::queryFirstRow(
        "SELECT * FROM vote_candidates WHERE vote_role=%d AND user_id=%d AND voting_id=%d",
        $role_id,
        $user_id,
        Settings::getVotingID(),
      );
    if (!$result) {
      return null;
    }
    return new VoteCandidate(new Map($result));
  }

  public static function loadRole(VoteRoleEnum $role): array<VoteCandidate> {
    $query = DB::query(
      'SELECT * FROM vote_candidates WHERE vote_role='.
      $role.
      ' AND voting_id='.
      Settings::getVotingID(),
    );
    if (!$query) {
      return array();
    }
    return array_map(
      function($value) {
        return new VoteCandidate(new Map($value));
      },
      $query,
    );
  }

  public static function loadRoleByScore(
    VoteRoleEnum $role,
  ): array<VoteCandidate> {
    $query = DB::query(
      "SELECT * FROM vote_candidates WHERE vote_role=".
      $role.
      ' AND voting_id='.
      Settings::getVotingID().
      ' ORDER BY score DESC',
    );
    if (!$query) {
      return array();
    }
    return array_map(
      function($value) {
        return new VoteCandidate(new Map($value));
      },
      $query,
    );
  }

  /* END MANUAL SECTION */
}
