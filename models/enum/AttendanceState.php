<?hh // strict

enum AttendanceState : int as int {
  NotPresent = 0;
  Present = 1;
  Excused = 2;
}

class AttendanceStateInfo {
  public static function toString(AttendanceState $state): string {
    switch ($state) {
      case AttendanceState::NotPresent:
        return 'Not Present';
      case AttendanceState::Present:
        return 'Present';
      case AttendanceState::Excused:
        return 'Excused';
    }
  }
}
