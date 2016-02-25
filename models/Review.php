<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<6a7441966ae075eb0772ee32f3f7c516>>
 */

final class Review {

  private function __construct(private Map<string, mixed> $data) {}

  public static function load(int $id): ?Review {
    $result = DB::queryFirstRow("SELECT * FROM reviews WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new Review(new Map($result));
  }

  public function getComments(): string {
    return (string) $this->data['comments'];
  }

  public function getRating(): int {
    return (int) $this->data['rating'];
  }

  public function getUserID(): int {
    return (int) $this->data['user_id'];
  }

  public function getAppID(): int {
    return (int) $this->data['application_id'];
  }

  /* BEGIN MANUAL SECTION Review_footer */
  public static function loadByUserAndApp(int $user_id, int $app_id): ?Review {
    $query = DB::queryFirstRow(
      "SELECT * FROM reviews WHERE user_id=%s AND application_id=%s",
      $user_id,
      $app_id,
    );
    if (!$query) {
      return null;
    }
    return new Review(new Map($query));
  }

  public static function loadByApp(int $app_id): array<Review> {
    $query =
      DB::query("SELECT * FROM reviews WHERE application_id=%s", $app_id);
    if (!$query) {
      return array();
    }
    return array_map(
      function($value) {
        return new Review(new Map($value));
      },
      $query,
    );
  }

  public static function getAppCount(int $app_id): int {
    $query = DB::queryFirstRow(
      "SELECT COUNT(*) FROM reviews WHERE application_id=%s",
      $app_id,
    );
    return (int) $query['COUNT(*)'];
  }

  public static function getAvgRating(int $app_id): string {
    $query = DB::queryFirstRow(
      "SELECT AVG(rating) FROM reviews WHERE application_id=%s",
      $app_id,
    );
    return number_format($query['AVG(rating)'], 2);
  }
  /* END MANUAL SECTION */
}
