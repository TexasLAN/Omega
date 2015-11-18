<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<94e58a4efcb9fbcee6a3ee3b4f85279d>>
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

  public function getForgotToken(): ?string {
    return isset($this->data['forgot_token'])
      ? (string) $this->data['forgot_token'] : null;
  }

  /* BEGIN MANUAL SECTION User_footer */
  public function getID(): int {
    return (int) $this->data['id'];
  }
  /*
    Loads the User obj from a string username
    Returns a user obj with the username or null if it doesnt exist.
   */
  public static function loadUsername(string $username): ?User {
    $result = DB::queryFirstRow("SELECT * FROM users WHERE username=%s", $username);
    if (!$result) {
      return null;
    }
    return new User(new Map($result));
  }

  /*
    Loads the User obj from a string email
    Returns a user obj with the email or null if it doesnt exist.
   */
  public static function loadEmail(string $email): ?User {
    $result = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $email);
    if (!$result) {
      return null;
    }
    return new User(new Map($result));
  }

  /*
    Loads the User obj from an id and a token
    Returns a user obj with the id and token or null if it doesnt exist.
   */
  public static function loadIdAndToken(int $id, string $token): ?User {
    $result = DB::queryFirstRow("SELECT * FROM users WHERE id=%d AND token=%s", $id, $token);
    if (!$result) {
      return null;
    }
    return new User(new Map($result));
  }

  /*
    Loads a list of users that match a vector of states.
    Returns a list of users, or null if there is none that match the user states.
   */
  public static function loadStates(Vector<UserState> $states): array<User> {
    $whereMsg = '';
    $delim = '';
    foreach($states as $state) {
      $whereMsg .= $delim . "member_status=" . $state;
      $delim = ' OR ';
    }

    $query = DB::query("SELECT * FROM users WHERE " . $whereMsg);
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new User(new Map($value));
    }, $query);
  }

  public static function loadRole(UserRoleEnum $role): array<User> {
    $query = DB::query("SELECT * FROM users, roles WHERE roles.user_id = users.id AND roles.role=%s", $role);
    
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new User(new Map($value));
    }, $query);
  }

  /*
    Loads the User obj from a string forgotToken
    Used for resetting passwords
    Returns a user obj with the email or null if it doesnt exist.
   */
  public static function loadForgotToken(string $forgotToken): ?User {
    $result = DB::queryFirstRow("SELECT * FROM users WHERE forgot_token=%s", $forgotToken);
    if (!$result) {
      return null;
    }
    return new User(new Map($result));
  }



  /*
    Gets the users roles from the UserRole object
   */
  public function getRoles(): array {
    return UserRole::getRoles($this->getID());
  }


  /*
    Checks if a user has the role that it is checking against
    Returns true if the user is that role, false if not
   */
  public function validateRole(UserRoleEnum $role): bool {
    return in_array($role, $this->getRoles());
  }

  /*
    Gets the UserState Enum in order to validate that it maps to a enum value
   */
  public function getState(): UserState {
    return UserState::assert($this->getMemberStatus());
  }

  /*
    Gets the UserState Enum in order to validate that it maps to a enum value and returns the string of it
   */
  public function getStateStr(): string {
    return UserState::getNames()[UserState::assert($this->getState())];
  }

  /*
    Checks the status and sees if it is reviewable(applicant/candidate) or not
   */
  public function isReviewable(): bool {
    return ($this->getState() == UserState::Applicant || $this->getState() == UserState::Candidate);
  }
  /* END MANUAL SECTION */
}
