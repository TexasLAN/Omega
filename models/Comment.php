<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<670d8e12d9178b4bff16934929eb1149>>
 */

final class Comment {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?Comment {
    $result = DB::queryFirstRow("SELECT * FROM comment WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new Comment(new Map($result));
  }

  public function getMessage(): string {
    return (string) $this->data['message'];
  }

  public function getStatus(): int {
    return (int) $this->data['status'];
  }

  /* BEGIN MANUAL SECTION Comment_footer */
  // Insert additional methods here
  public function getID(): int {
    return (int) $this->data['id'];
  }
  
  public function getMessageXHP(): :div {
    $Parsedown = new Parsedown();
    $parsed_message = $Parsedown->text($this->data['message']);
    return <div><omega:email-message message={$parsed_message} /></div>;
  }

  public static function loadByStatus(
    CommentStatus $status,
  ): array<Comment> {
    $query = DB::query("SELECT * FROM comment where status=%d", $status);
    if (!$query) {
      return array();
    }

    return array_map(
      function($value) {
        return new Comment(new Map($value));
      },
      $query,
    );
  }
  /* END MANUAL SECTION */
}
