<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<78fa7c95dc4e94bf02dac131bd132622>>
 */

final class SuggestionsMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new SuggestionsMutator();
  }

  public static function update(int $id): this {
    return new SuggestionsMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("suggestions", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("suggestions", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("suggestions", $this->data->toArray(), "id=%s", $this->id);
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

  /* BEGIN MANUAL SECTION SuggestionsMutator_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
