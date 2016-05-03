<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<915dc9b27dbe0b6423214c3996567a55>>
 */

final class CommentMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new CommentMutator();
  }

  public static function update(int $id): this {
    return new CommentMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("comment", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("comment", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("comment", $this->data->toArray(), "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'message',
      'status',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setMessage(string $value): this {
    $this->data["message"] = $value;
    return $this;
  }

  public function setStatus(int $value): this {
    $this->data["status"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION CommentMutator_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
