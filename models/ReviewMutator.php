<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<bdd57fa1c7f28cd4fc62360706c8c622>>
 */

final class ReviewMutator {

  private Map<string, mixed> $data = Map {};

  private function __construct(private ?int $id = null) {}

  public static function create(): this {
    return new ReviewMutator();
  }

  public static function update(int $id): this {
    return new ReviewMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("reviews", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("reviews", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("reviews", $this->data->toArray(), "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {'comments', 'rating', 'user_id', 'application_id'};
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

  public function setRating(int $value): this {
    $this->data["rating"] = $value;
    return $this;
  }

  public function setUserID(int $value): this {
    $this->data["user_id"] = $value;
    return $this;
  }

  public function setAppID(int $value): this {
    $this->data["application_id"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION ReviewMutator_footer */
  public static function upsert(
    string $comments,
    int $rating,
    User $user,
    Application $application,
  ): void {
    DB::query(
      "SELECT * FROM reviews WHERE user_id=%s AND application_id=%s",
      $user->getID(),
      $application->getID(),
    );

    if (DB::count() != 0) {
      $paramData = Map {'comments' => $comments, 'rating' => $rating};
      DB::update(
        'reviews',
        $paramData->toArray(),
        'user_id=%s AND application_id=%s',
        $user->getID(),
        $application->getID(),
      );
    } else {
      $paramData = Map {
        'comments' => $comments,
        'rating' => $rating,
        'user_id' => $user->getID(),
        'application_id' => $application->getID(),
      };
      DB::insert('reviews', $paramData->toArray());
    }
  }
  /* END MANUAL SECTION */
}
