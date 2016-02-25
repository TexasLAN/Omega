<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<03bcd579e3d12445c1fdd61e61b38774>>
 */

final class Attendance {

  private function __construct(private Map<string, mixed> $data) {}

  public static function load(int $id): ?Attendance {
    $result = DB::queryFirstRow("SELECT * FROM attendance WHERE =%s", $id);
    if (!$result) {
      return null;
    }
    return new Attendance(new Map($result));
  }

  public function getUserID(): int {
    return (int) $this->data['user_id'];
  }

  public function getEventID(): int {
    return (int) $this->data['event_id'];
  }

  public function getStatus(): int {
    return (int) $this->data['status'];
  }

  /* BEGIN MANUAL SECTION Attendance_footer */
  // Insert additional methods here
  public static function loadForUserEvent(
    int $user_id,
    int $event_id,
  ): ?Attendance {
    $result = DB::queryFirstRow(
      "SELECT * FROM attendance WHERE user_id=%s AND event_id=%s",
      $user_id,
      $event_id,
    );
    if (!$result) {
      return null;
    }
    return new Attendance(new Map($result));
  }
  public static function loadForEvent(int $event_id): array<Attendance> {
    $query = DB::query("SELECT *
      FROM attendance
      WHERE event_id=%s", $event_id);
    if (!$query) {
      return array();
    }
    return array_map(
      function($value) {
        return new Attendance(new Map($value));
      },
      $query,
    );
  }

  public static function loadForUser(int $user_id): array<Attendance> {
    $query = DB::query("
      SELECT *
      FROM attendance
      WHERE user_id=%s", $user_id);
    if (!$query) {
      return array();
    }
    return array_map(
      function($value) {
        return new Attendance(new Map($value));
      },
      $query,
    );
  }

  public static function countUserAttendance(
    int $user_id,
    AttendanceState $attendance,
    ?EventType $type,
  ): int {
    if (!is_null($type)) {
      $query =
        DB::queryFirstRow(
          "
        SELECT COUNT(user_id)
        FROM attendance, events
        WHERE attendance.status=%s AND attendance.user_id=%s AND events.type=%s AND events.id = attendance.event_id",
          $attendance,
          $user_id,
          $type,
        );
    } else {
      $query =
        DB::queryFirstRow(
          "
        SELECT COUNT(user_id)
        FROM attendance, events
        WHERE attendance.status=%s AND attendance.user_id=%s AND events.id = attendance.event_id",
          $attendance,
          $user_id,
        );
    }
    return (int) $query['COUNT(user_id)'];
  }
  /* END MANUAL SECTION */
}
