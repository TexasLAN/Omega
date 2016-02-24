<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<dfb24d766621e3e557bd8f98953469a7>>
 */

final class Suggestions {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?Suggestions {
    $result = DB::queryFirstRow("SELECT * FROM suggestions WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new Suggestions(new Map($result));
  }

  public function getMessage(): string {
    return (string) $this->data['message'];
  }

  public function getStatus(): int {
    return (int) $this->data['status'];
  }

  /* BEGIN MANUAL SECTION Suggestions_footer */
  public function getID(): int {
    return (int) $this->data['id'];
  }

  public function getMessageXHP(): :div {
    $Parsedown = new Parsedown();
    $parsed_message = $Parsedown->text($this->data['message']);
    return <div><omega:email-message message={$parsed_message} /></div>;
  }
  // Insert additional methods here
  public static function loadByStatus(SuggestionStatus $status): array<Suggestions> {
    $query = DB::query("SELECT * FROM suggestions where status=%d", $status);
    if(!$query) {
      return array();
    }

    return array_map(function($value) {
      return new Suggestions(new Map($value));
    }, $query);
  }
  /* END MANUAL SECTION */
}
