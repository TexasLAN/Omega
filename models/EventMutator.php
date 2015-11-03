<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<118634c6ccf7e047e7c4a5e909579af5>>
 */

final class EventMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new EventMutator();
  }

  public static function update(int $id): this {
    return new EventMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("events", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("events", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("events", $this->data->toArray(), "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'name',
      'location',
      'start_date',
      'end_date',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setName(string $value): this {
    $this->data["name"] = $value;
    return $this;
  }

  public function setLocation(string $value): this {
    $this->data["location"] = $value;
    return $this;
  }

  public function setStartDate(DateTime $value): this {
    $this->data["start_date"] = $value->format("Y-m-d");
    return $this;
  }

  public function setEndDate(DateTime $value): this {
    $this->data["end_date"] = $value->format("Y-m-d");
    return $this;
  }

  /* BEGIN MANUAL SECTION EventMutator_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
