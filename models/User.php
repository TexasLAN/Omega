<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<33824bb95d4d84a8e445359a2f36d0df>>
 */

final class User {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?User {
    $result = DB::queryFirstRow("SELECT * FROM users WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new User(new Map($result));
  }

  public function getID(): int {
    return (int) $this->data['id'];
  }

  public function getEmail(): string {
    return (string) $this->data['email'];
  }

  public function getFirstName(): string {
    return (string) $this->data['fname'];
  }

  public function getLastName(): string {
    return (string) $this->data['lname'];
  }

  public function getUsername(): string {
    return (string) $this->data['username'];
  }

  public function getPassword(): string {
    return (string) $this->data['password'];
  }

  public function getMemberStatus(): int {
    return (int) $this->data['member_status'];
  }

  public function getToken(): ?string {
    return isset($this->data['token']) ? (string) $this->data['token'] : null;
  }

  /* BEGIN MANUAL SECTION User_footer */
  // Insert additional methods here
  public static function loadUsername(string $username): ?User {
    $result = DB::queryFirstRow("SELECT * FROM users WHERE username=%s", $username);
    if (!$result) {
      return null;
    }
    return new User(new Map($result));
  }

  public static function loadEmail(string $email): ?User {
    $result = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $email);
    if (!$result) {
      return null;
    }
    return new User(new Map($result));
  }

  public static function loadIdAndToken(int $id, string $token): ?User {
    $result = DB::queryFirstRow("SELECT * FROM users WHERE id=%d AND token=%s", $id, $token);
    if (!$result) {
      return null;
    }
    return new User(new Map($result));
  }

  public function getRoles(): array {
    return UserRole::getRoles($this->getID());
  }

  public function validateRole(UserRoleEnum $role): bool {
    return in_array($role, $this->getRoles());
  }

  public function getUserState(): UserState {
    return UserState::assert($this->getMemberStatus());
  }

  public function getUserStateStr(): string {
    return UserState::getNames()[UserState::assert($this->getUserState())];
  }
  /* END MANUAL SECTION */
}
