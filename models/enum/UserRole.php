<?hh

enum UserRoleEnum : string as string {
  Admin = 'admin';
  Reviewer = 'reviewer';
  Officer = 'officer';
  Standards = 'standards';
  Webmaster = 'webmaster';
}

class UserRole {
  public static function insert(string $role, int $user_id): void {
    $paramData = Map {'role' => $role, 'user_id' => $user_id};
    DB::insert('roles', $paramData->toArray());
  }

  public static function delete(string $role, int $user_id): void {
    DB::delete('roles', 'user_id=%s AND role=%s', $user_id, $role);
  }

  public static function getRoles(int $user_id): array {
    $query = DB::query("SELECT role FROM roles WHERE user_id=%s", $user_id);
    $roles = array_map(
      function($value) {
        return $value['role'];
      },
      $query,
    );
    return $roles;
  }
}
