<?hh

class UserOld {

  private int $id = 0;
  private string $username = '';
  private string $password = '';
  private string $email = '';
  private string $fname = '';
  private string $lname = '';
  private string $token = '';
  private int $member_status = 0;
  private array $roles = array();

  public static function create(
    $username,
    $password,
    $email,
    $fname,
    $lname
  ): ?UserOld {
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
    DB::insert('users',  Map {
      'username' => $username,
      'password' => $hash,
      'email' => $email,
      'fname' => $fname,
      'lname' => $lname,
      'member_status' => 0
    });

    return self::genByUsername($username);
  }

  public function setToken(string $token): void {
    DB::update('users', Map {
      'token' => $token
    }, 'id=%s', $this->id);
    $this->token = $token;
  }

  public function getID():int {
    return $this->id;
  }

  public function getUsername(): string {
    return $this->username;
  }

  public function getPassword(): string {
    return $this->password;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function getFirstName(): string {
    return $this->fname;
  }

  public function getLastName(): string {
    return $this->lname;
  }

  public function getRoles(): array {
    return $this->roles;
  }

  public function getStatusID(): int {
    return $this->member_status;
  }

  public function getStatus(): string {
    switch($this->member_status) {
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
    return $this->member_status == UserState::Applicant;
  }

  public function isPledge(): bool {
    return $this->member_status == UserState::Pledge;
  }

  public function isMember(): bool {
    return $this->member_status == UserState::Member;
  }

  public function isDisabled(): bool {
    return $this->member_status == UserState::Disabled;
  }

  public function isInactive(): bool {
    return $this->member_status == UserState::Inactive;
  }

  public function isAlum(): bool {
    return $this->member_status == UserState::Alum;
  }

  public function isAdmin(): bool {
    return in_array('admin', $this->roles);
  }

  public function isReviewer(): bool {
    return in_array('reviewer', $this->roles);
  }

  public function isOfficer(): bool {
    return in_array(Roles::Officer, $this->roles);
  }

  public static function genByID($user_id): ?UserOld {
    return self::constructFromQuery('id', $user_id);
  }

  public static function genByUsername($username): ?UserOld {
    return self::constructFromQuery('username', $username);
  }

  public static function genByEmail($email): ?UserOld {
    return self::constructFromQuery('email', $email);
  }

  public static function genByIDAndToken(int $user_id, string $token): ?UserOld {
    $query = DB::queryFirstRow("SELECT * FROM users WHERE id=%s AND token=%s", $user_id, $token);
    if(!$query) {
      return null;
    }
    $user = self::createFromQuery($query);
    return $user;
  }

  public static function updateStatusByID(int $status, int $user_id): void {
    DB::update('users', Map {'member_status' => $status}, "id=%s", $user_id);
  }

  public static function deleteByID($user_id): void {
    DB::delete('users', 'id=%s', $user_id);
  }

  private static function constructFromQuery($field, $query): ?UserOld {
    # Get the user
    $query = DB::queryFirstRow("SELECT * FROM users WHERE " . $field ."=%s", $query);
    if(!$query) {
      return null;
    }
    $user = self::createFromQuery($query);
    //$user->roles = UserRole::getRoles($user->getID());

    return $user;
  }

  private static function createFromQuery(array $query): UserOld {
    $user = new UserOld();
    $user->id = (int)$query['id'];
    $user->username = $query['username'];
    $user->password = $query['password'];
    $user->email = $query['email'];
    $user->fname = $query['fname'];
    $user->lname = $query['lname'];
    $user->token = $query['token'];
    $user->member_status = (int)$query['member_status'];
    return $user;
  }
}
