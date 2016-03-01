<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<0a441ecd3d69635fee4119021c023d23>>
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
      'email',
      'full_name',
      'nick_name',
      'username',
      'password',
      'member_status',
      'phone_number',
      'grad_year',
      'has_voted',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setEmail(string $value): this {
    $this->data["email"] = $value;
    return $this;
  }

  public function setFullName(string $value): this {
    $this->data["full_name"] = $value;
    return $this;
  }

  public function setNickName(string $value): this {
    $this->data["nick_name"] = $value;
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

  public function setPhoneNumber(string $value): this {
    $this->data["phone_number"] = $value;
    return $this;
  }

  public function setGraduationYear(int $value): this {
    $this->data["grad_year"] = $value;
    return $this;
  }

  public function setHasVoted(bool $value): this {
    $this->data["has_voted"] = $value;
    return $this;
  }

  public function setClass(int $value): this {
    $this->data["class"] = $value;
    return $this;
  }

  public function setToken(string $value): this {
    $this->data["token"] = $value;
    return $this;
  }

  public function setForgotToken(string $value): this {
    $this->data["forgot_token"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION UserMutator_footer */
  // Insert additional methods here
  public static function createUser(
    string $username,
    string $password,
    string $email,
    string $phone,
    string $full_name,
    string $nick_name,
  ): ?User {
    // Make sure a user doesn't already exist with that username or email
    DB::query(
      "SELECT * FROM users WHERE username=%s OR email=%s",
      $username,
      $email,
    );
    if (DB::count() != 0) {
      return null;
    }
    // Insert the user
    $paramData = Map {
      'username' => $username,
      'password' => self::encryptPassword($password),
      'email' => $email,
      'phone_number' => $phone,
      'full_name' => $full_name,
      'nick_name' => $nick_name,
      'member_status' => 0,
      'has_voted' => false,
    };
    DB::insert('users', $paramData->toArray());
    return User::loadUsername($username);
  }

  public static function updatePassword(int $userID, string $password): void {
    UserMutator::update($userID)
      ->setForgotToken('')
      ->setPassword(self::encryptPassword($password))
      ->save();
  }

  public static function encryptPassword(string $password): string {
    $salt = strtr(
      base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)),
      '+',
      '.',
    );
    $salt = sprintf("$2a$%02d$", 10).$salt;
    $hash = crypt($password, $salt);
    return $hash;
  }

  public static function deleteByState(UserState $state): void {
    DB::delete("users", "member_status=%s", $state);
  }

  public static function disableByState(UserState $state): void {
    $paramData = Map {'member_status' => UserState::Disabled};
    DB::update("users", $paramData->toArray(), "member_status=%s", $state);
  }
  /* END MANUAL SECTION */
}
