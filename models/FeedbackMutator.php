<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<13ccb44d679b1c89a91798339cc3a538>>
 */

final class FeedbackMutator {

  private Map<string, mixed> $data = Map {};

  private function __construct(private ?int $id = null) {}

  public static function create(): this {
    return new FeedbackMutator();
  }

  public static function update(int $id): this {
    return new FeedbackMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("feedback", "=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("feedback", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("feedback", $this->data->toArray(), "=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {'comments', 'user_id', 'reviewer_id'};
    $missing = $required->removeAll($this->data->keys());
    ;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setComments(string $value): this {
    $this->data["comments"] = $value;
    return $this;
  }

  public function setUserID(int $value): this {
    $this->data["user_id"] = $value;
    return $this;
  }

  public function setReviewerID(int $value): this {
    $this->data["reviewer_id"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION FeedbackMutator_footer */
  public static function upsert(
    string $comments,
    int $user_id,
    int $reviewer_id,
  ): void {
    DB::query(
      "SELECT * FROM feedback WHERE user_id=%s AND reviewer_id=%s",
      $user_id,
      $reviewer_id,
    );

    if (DB::count() != 0) {
      $paramData = Map {'comments' => $comments};
      DB::update(
        'feedback',
        $paramData->toArray(),
        'user_id=%s AND reviewer_id=%s',
        $user_id,
        $reviewer_id,
      );
    } else {
      $paramData = Map {
        'comments' => $comments,
        'user_id' => $user_id,
        'reviewer_id' => $reviewer_id,
      };
      DB::insert('feedback', $paramData->toArray());
    }
  }
  /* END MANUAL SECTION */
}
