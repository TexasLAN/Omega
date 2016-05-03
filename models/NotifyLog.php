<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<434f7624f34c90707750597385ed731d>>
 */

final class NotifyLog {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?NotifyLog {
    $result = DB::queryFirstRow("SELECT * FROM notify_log WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new NotifyLog(new Map($result));
  }

  public function getNotifyTitle(): string {
    return (string) $this->data['notify_title'];
  }

  public function getNotifyText(): string {
    return (string) $this->data['notify_text'];
  }

  public function getSenderUserId(): int {
    return (int) $this->data['sender_user_id'];
  }

  public function getSentTime(): DateTime {
    return new DateTime($this->data['sent_time']);
  }

  public function getHtmlParsed(): bool {
    return (bool) $this->data['html_parsed'];
  }

  /* BEGIN MANUAL SECTION NotifyLog_footer */
  public static function loadAllDesc(): array<NotifyLog> {
    $query = DB::query("SELECT * FROM notify_log ORDER BY id DESC");

    if (!$query) {
      return array();
    }
    return array_map(
      function($value) {
        return new NotifyLog(new Map($value));
      },
      $query,
    );
  }
  /* END MANUAL SECTION */
}
