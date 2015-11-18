<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<9bd80728877a1313dd17edd36f04ca3c>>
 */

final class Application {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?Application {
    $result = DB::queryFirstRow("SELECT * FROM applications WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new Application(new Map($result));
  }

  public function getGender(): string {
    return (string) $this->data['gender'];
  }

  public function getYear(): string {
    return (string) $this->data['year'];
  }

  public function getQuestion1(): string {
    return (string) $this->data['q1'];
  }

  public function getQuestion2(): string {
    return (string) $this->data['q2'];
  }

  public function getQuestion3(): string {
    return (string) $this->data['q3'];
  }

  public function getQuestion4(): string {
    return (string) $this->data['q4'];
  }

  public function getQuestion5(): string {
    return (string) $this->data['q5'];
  }

  public function getQuestion6(): string {
    return (string) $this->data['q6'];
  }

  public function getUserID(): int {
    return (int) $this->data['user_id'];
  }

  public function getStatus(): int {
    return (int) $this->data['status'];
  }

  /* BEGIN MANUAL SECTION Application_footer */
  public function getID(): int {
    return (int) $this->data['id'];
  }

  public static function loadByUser(int $user_id): ?Application {
    $result = DB::queryFirstRow("SELECT * FROM applications WHERE user_id=%s", $user_id);
    if (!$result) {
      return null;
    }
    return new Application(new Map($result));
  }

  public static function loadState(ApplicationState $state): array<Application> {

    $query = DB::query("SELECT * FROM applications WHERE status=%s", $state);
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Application(new Map($value));
    }, $query);
  }

  public function getAppState(): ApplicationState {
    return ApplicationState::assert($this->getStatus());
  }

  public function isStarted(): bool {
    return $this->getAppState() == ApplicationState::Started;
  }

  public function isSubmitted(): bool {
    return $this->getAppState() == ApplicationState::Submitted;
  }
  /* END MANUAL SECTION */
}
