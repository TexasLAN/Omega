<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<4363bbdcafa75b0f93ba17b9ac295e7f>>
 */

final class VoteCandidate {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?VoteCandidate {
    $result = DB::queryFirstRow("SELECT * FROM vote_candidates WHERE id=%s", $id);
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

  /* BEGIN MANUAL SECTION VoteCandidate_footer */
  // Insert additional methods here

  public static function loadByRoleAndUser(int $role_id, int $user_id): ?VoteCandidate {
    $result = DB::queryFirstRow("SELECT * FROM vote_candidates WHERE vote_role=%d AND user_id=%d", $role_id, $user_id);
    if (!$result) {
      return null;
    }
    return new VoteCandidate(new Map($result));
  }

  public static function loadRole(VoteRoleEnum $role): array<VoteCandidate> {
    $whereMsg = '';
    $delim = '';
    $whereMsg .= $delim . "vote_role=" . $role;

    $query = DB::query("SELECT * FROM vote_candidates WHERE " . $whereMsg);
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new VoteCandidate(new Map($value));
    }, $query);
  }

  public static function loadRoleByScore(VoteRoleEnum $role): array<VoteCandidate> {
    $whereMsg = '';
    $delim = '';
    $whereMsg .= $delim . "vote_role=" . $role;

    $query = DB::query("SELECT * FROM vote_candidates WHERE " . $whereMsg . " ORDER BY score DESC");
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new VoteCandidate(new Map($value));
    }, $query);
  }

  /* END MANUAL SECTION */
}
