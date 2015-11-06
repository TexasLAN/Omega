<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<066cebac12f1145ca8da75344f9def96>>
 */

final class Feedback {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?Feedback {
    $result = DB::queryFirstRow("SELECT * FROM feedback WHERE =%s", $id);
    if (!$result) {
      return null;
    }
    return new Feedback(new Map($result));
  }

  public function getComments(): string {
    return (string) $this->data['comments'];
  }

  public function getUserID(): int {
    return (int) $this->data['user_id'];
  }

  public function getReviewerID(): int {
    return (int) $this->data['reviewer_id'];
  }

  /* BEGIN MANUAL SECTION Feedback_footer */
  // Insert additional methods here
  public static function loadByUserAndReviewer(int $user_id, int $reviewer_id): ?Feedback {
    $result = DB::queryFirstRow("SELECT * FROM feedback WHERE user_id=%s AND reviewer_id=%s", $user_id, $reviewer_id);
    if (!$result) {
      return null;
    }
    return new Feedback(new Map($result));
  }

  public static function loadByUser(int $user_id): array<Feedback> {
    $query = DB::query("SELECT * FROM feedback WHERE user_id=%s", $user_id);
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Feedback(new Map($value));
    }, $query);
  }
  /* END MANUAL SECTION */
}
