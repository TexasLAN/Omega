<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<6df940aef94e623a7153e2d81db38190>>
 */

final class Event {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?Event {
    $result = DB::queryFirstRow("SELECT * FROM events WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new Event(new Map($result));
  }

  public function getName(): string {
    return (string) $this->data['name'];
  }

  public function getLocation(): string {
    return (string) $this->data['location'];
  }

  public function getStartDate(): DateTime {
    return new DateTime($this->data['start_date']);
  }

  public function getEndDate(): DateTime {
    return new DateTime($this->data['end_date']);
  }

  public function getType(): string {
    return (string) $this->data['type'];
  }

  public function getDescription(): string {
    return (string) $this->data['description'];
  }

  /* BEGIN MANUAL SECTION Event_footer */
  // Insert additional methods here
  public static function strToDatetime(string $date, string $time): DateTime {
    $datetime = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
    if (!$datetime) { // Checks if it is some weird javashit nonsense
      $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
    }
    return $datetime;
  }

  public static function datetimeToStr(DateTime $date): string {
    return $date->format('n/j/Y \@ g:i A');
  }

  // Converts the datetime to a javascript standard for the event modal
  public static function datetimeToWeb(DateTime $date): string {
    $result = $date->format('Y-m-d@H:i:s');
    // The 'T' means some date format thing so use @ as workaround
    $result = ereg_replace('@', 'T', $result);
    return $result;
  }

  public function getID(): int {
    return (int) $this->data['id'];
  }

  public static function loadRecentCreated(): Event {
    $result = DB::queryFirstRow("SELECT * FROM events ORDER BY id DESC");
    return new Event(new Map($result));
  }

  public static function loadFuture(): array<Event> {
    $query = DB::query("
      SELECT * FROM events
      WHERE start_date >= CURDATE()
      ORDER BY start_date ASC"
    );
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Event(new Map($value));
    }, $query);
  }

  public static function loadPast(): array<Event> {
    $query = DB::query("
      SELECT * FROM events
      WHERE start_date < CURDATE()
      ORDER BY start_date DESC"
    );
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Event(new Map($value));
    }, $query);
  }
  /* END MANUAL SECTION */
}
