<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<c389f62ad8f10ed1ca2243e2ef969935>>
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
      DB::insert("users", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("users", $this->data->toArray(), "id=%s", $this->id);
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
  public static function createUser(
    string $username,
    string $password,
    string $email,
    string $fname,
    string $lname
  ): ?User {
    # Make sure a user doesn't already exist with that username or email
    DB::query(
      "SELECT * FROM users WHERE username=%s OR email=%s",
      $username, $email
    );
    if(DB::count() != 0) {
      return null;
    }
    # Create the password hash
    $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
    $salt = sprintf("$2a$%02d$", 10) . $salt;
    $hash = crypt($password, $salt);
    # Insert the user
    $paramData = Map {
      'username' => $username,
      'password' => $hash,
      'email' => $email,
      'fname' => $fname,
      'lname' => $lname,
      'member_status' => 0
    };
    DB::insert('users', $paramData->toArray());
    return User::loadUsername($username);
  }
  /* END MANUAL SECTION */
}
