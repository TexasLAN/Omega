<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<d27d78d90e03822bec74c0ea021a1dee>>
 */

final class AttendanceMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new AttendanceMutator();
  }

  public static function update(int $id): this {
    return new AttendanceMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("attendance", "=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("attendance", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("attendance", $this->data->toArray(), "=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'user_id',
      'event_id',
      'status',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setUserID(int $value): this {
    $this->data["user_id"] = $value;
    return $this;
  }

  public function setEventID(int $value): this {
    $this->data["event_id"] = $value;
    return $this;
  }

  public function setStatus(int $value): this {
    $this->data["status"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION AttendanceMutator_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
