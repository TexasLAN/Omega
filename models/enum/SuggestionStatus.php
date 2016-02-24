<?hh

enum SuggestionStatus: int as int {

  Open = 0;
  Closed = 1;

}

class SuggestionStatusInfo {
	public static function getName(int $value): string {
		switch ($value) {
			case SuggestionStatus::Open:
				return 'Open';
			case SuggestionStatus::Closed:
				return 'Closed';
			default:
				return '';
		}
	}
}
