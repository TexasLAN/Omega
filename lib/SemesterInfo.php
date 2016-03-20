<?hh

enum Semester : int as int {
  Spring = 0;
  Summer = 1;
  Fall = 2;
}

class SemesterInfo {

	public static function getSemesterForDate(DateTime $date): ?Semester {
		$month = (int) $date->format('m');

		if($month >= 1 && $month <= 5) {
			return Semester::Spring;
		}

		if($month >= 6 && $month <= 7) {
			return Semester::Summer;
		}

		if($month >= 8 && $month <= 12) {
			return Semester::Fall;
		}
		
		return null;

	}

	public static function isEventCurrentSemester(Event $event) :bool {
		$sameYear = $event->getStartDate()->format('y') == (new DateTime())->format('y');
		return $sameYear && (self::getSemesterForDate($event->getStartDate()) == self::getSemesterForDate(new DateTime()));
	}
}