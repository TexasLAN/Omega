<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<927d13d12864f919b80968d0a8ec260b>>
 */

final class UserMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new UserMutator();
  }

  public static function update(int $id): this {
    return new UserMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("users", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("users", $this->data);
      return (int) DB::insertId();
    } else {
      DB::update("users", $this->data, "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'id',
      'email',
      'fname',
      'lname',
      'username',
      'password',
      'member_status',
      'token',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setID(int $value): this {
    $this->data["id"] = $value;
    return $this;
  }

  public function setEmail(string $value): this {
    $this->data["email"] = $value;
    return $this;
  }

  public function setFirstName(string $value): this {
    $this->data["fname"] = $value;
    return $this;
  }

  public function setLastName(string $value): this {
    $this->data["lname"] = $value;
    return $this;
  }

  public function setUsername(string $value): this {
    $this->data["username"] = $value;
    return $this;
  }

  public function setPassword(string $value): this {
    $this->data["password"] = $value;
    return $this;
  }

  public function setMemberStatus(int $value): this {
    $this->data["member_status"] = $value;
    return $this;
  }

  public function setToken(string $value): this {
    $this->data["token"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION UserMutator_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
