<?hh

class Settings {
  public static function get(string $key): bool {
    $query = DB::queryFirstRow("SELECT * FROM settings WHERE name=%s", $key);
    if (!$query) {
      return false;
    }
    return filter_var($query['value'], FILTER_VALIDATE_BOOLEAN);
  }

  public static function getCurrentClass(): int {
    $query =
      DB::queryFirstRow("SELECT * FROM settings WHERE name=%s", "cur_class");
    if (!$query) {
      return 0;
    }
    return (int) $query['value'];
  }

  public static function getVotingID(): int {
    $query =
      DB::queryFirstRow("SELECT * FROM settings WHERE name=%s", "voting_id");
    if (!$query) {
      return 0;
    }
    return (int) $query['value'];
  }

  public static function getVotingStatus(): VotingStatus {
    $query = DB::queryFirstRow(
      "SELECT * FROM settings WHERE name=%s",
      "voting_status",
    );
    if (!$query) {
      return VotingStatus::Closed;
    }
    return VotingStatus::assert($query['value']);
  }

  public static function set(string $key, mixed $value): void {
    $paramData = Map {'name' => $key, 'value' => $value};
    DB::insertUpdate('settings', $paramData->toArray());
  }
}
