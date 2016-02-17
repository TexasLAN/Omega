<?hh // strict

enum AttendanceState : int as int {
	NotPresent = 0;
	Present = 1;
}

class AttendanceStateInfo {
	public static function toString(AttendanceState $state): string {
		switch ($state) {
			case AttendanceState::NotPresent: return 'Not Present';
			case AttendanceState::Present: return 'Present';
		}
	}
}