<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<b68a7e2be5a30314b68b960f4d49c030>>
 */

final class NotifyLogMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new NotifyLogMutator();
  }

  public static function update(int $id): this {
    return new NotifyLogMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("notify_log", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("notify_log", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("notify_log", $this->data->toArray(), "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'notify_title',
      'notify_text',
      'sender_user_id',
      'sent_time',
      'html_parsed',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setNotifyTitle(string $value): this {
    $this->data["notify_title"] = $value;
    return $this;
  }

  public function setNotifyText(string $value): this {
    $this->data["notify_text"] = $value;
    return $this;
  }

  public function setSenderUserId(int $value): this {
    $this->data["sender_user_id"] = $value;
    return $this;
  }

  public function setSentTime(DateTime $value): this {
    $this->data["sent_time"] = $value->format("Y-m-d");
    return $this;
  }

  public function setHtmlParsed(bool $value): this {
    $this->data["html_parsed"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION NotifyLogMutator_footer */
  // Insert additional methods here
  public function _setSentTime(DateTime $value): this {
    $this->data["sent_time"] = $value->format("Y-m-d H:i");
    return $this;
  }
  /* END MANUAL SECTION */
}
