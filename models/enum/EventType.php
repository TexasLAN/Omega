<?hh // strict

enum EventType : string as string {
  OfficerMeeting = 'om';
  GeneralMeeting = 'gm';
  PledgeBonding = 'pb';
  Service = 'service';
  Special = 'lan';
  Other = 'other';
}

class EventTypeInfo {
	public static function getEventTypeName(string $val): string {
		switch($val) {
      case EventType::OfficerMeeting:
        return 'Officer Meeting';
      case EventType::GeneralMeeting:
        return 'General Meeting';
      case EventType::PledgeBonding:
        return 'Pledge Bonding';
      case EventType::Service:
        return 'Service';
      case EventType::Special:
        return 'Special LAN';
      case EventType::Other:
        return 'Other';
      default:
        return '';
		}
	}

	public static function getPoints(string $val): int {
		switch($val) {
      case EventType::OfficerMeeting:
        return 1;
      case EventType::GeneralMeeting:
        return 3;
      case EventType::PledgeBonding:
        return 2;
      case EventType::Service:
        return 3;
      case EventType::Special:
        return 6;
      case EventType::Other:
      default:
        return 0;
		}
	}
}
