<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<6def5a1872bc172ef8c3f7970ed3a06c>>
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

  /* BEGIN MANUAL SECTION Event_footer */
  // Insert additional methods here
  public static function strToDatetime(string $date, string $time): DateTime {
    $datetime = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
    if (!$datetime) {
      $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
    }
    return $datetime;
  }

  public static function datetimeToStr(DateTime $date): string {
    // $timestamp = strtotime($date);
    return $date->format('n/j/Y \@ g:i A');
    // return date('n/j/Y \@ g:i A', $timestamp);
  }

  public static function datetimeToWeb(DateTime $date): string {
    // $timestamp = strtotime($date);
    $result = $date->format('Y-m-d@H:i:s');
    $result = ereg_replace('@', 'T', $result);
    return $result;
    // return date('n/j/Y \@ g:i A', $timestamp);
  }

  public function getID(): int {
    return (int) $this->data['id'];
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
