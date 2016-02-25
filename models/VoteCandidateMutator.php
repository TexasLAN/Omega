<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<575a174cf29970ef9d5b036e43331d7c>>
 */

final class VoteCandidateMutator {

  private Map<string, mixed> $data = Map {};

  private function __construct(private ?int $id = null) {}

  public static function create(): this {
    return new VoteCandidateMutator();
  }

  public static function update(int $id): this {
    return new VoteCandidateMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("vote_candidates", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("vote_candidates", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update(
        "vote_candidates",
        $this->data->toArray(),
        "id=%s",
        $this->id,
      );
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'vote_role',
      'user_id',
      'score',
      'description',
      'voting_id',
    };
    $missing = $required->removeAll($this->data->keys());
    ;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setVoteRole(int $value): this {
    $this->data["vote_role"] = $value;
    return $this;
  }

  public function setUserID(int $value): this {
    $this->data["user_id"] = $value;
    return $this;
  }

  public function setScore(int $value): this {
    $this->data["score"] = $value;
    return $this;
  }

  public function setDescription(string $value): this {
    $this->data["description"] = $value;
    return $this;
  }

  public function setVotingID(int $value): this {
    $this->data["voting_id"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION VoteCandidateMutator_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
