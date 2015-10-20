<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<db3479751349648b3bed413bfdc78828>>
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

  public function getToken(): string {
    return (string) $this->data['token'];
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

  public function getStatus(): string {
    switch($this->getMemberStatus()) {
      case UserState::Applicant:
        return 'applicant';
      case UserState::Pledge:
        return 'pledge';
      case UserState::Member:
        return 'member';
      case UserState::Disabled:
        return 'disabled';
      case UserState::Inactive:
        return 'inactive';
      case UserState::Alum:
        return 'alum';
      default:
        return 'unknown';
    }
  }

  public function isApplicant(): bool {
    return $this->getMemberStatus() == UserState::Applicant;
  }

  public function isPledge(): bool {
    return $this->getMemberStatus() == UserState::Pledge;
  }

  public function isMember(): bool {
    return $this->getMemberStatus() == UserState::Member;
  }

  public function isDisabled(): bool {
    return $this->getMemberStatus() == UserState::Disabled;
  }

  public function isInactive(): bool {
    return $this->getMemberStatus() == UserState::Inactive;
  }

  public function isAlum(): bool {
    return $this->getMemberStatus() == UserState::Alum;
  }

  public function isAdmin(): bool {
    return in_array('admin', $this->getRoles());
  }

  public function isReviewer(): bool {
    return in_array('reviewer', $this->getRoles());
  }

  public function isOfficer(): bool {
    return in_array(UserRoleEnum::Officer, $this->getRoles());
  }
  /* END MANUAL SECTION */
}
