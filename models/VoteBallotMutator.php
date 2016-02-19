<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<15115a64233b6bf95695674c6aadae89>>
 */

final class VoteBallotMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new VoteBallotMutator();
  }

  public static function update(int $id): this {
    return new VoteBallotMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("vote_ballot", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("vote_ballot", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("vote_ballot", $this->data->toArray(), "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'voting_id',
      'vote_list',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setVotingID(int $value): this {
    $this->data["voting_id"] = $value;
    return $this;
  }

  public function setVoteList(string $value): this {
    $this->data["vote_list"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION VoteBallotMutator_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
