<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<8e97f45826c739077accd65fb58c5285>>
 */

final class ApplicationMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new ApplicationMutator();
  }

  public static function update(int $id): this {
    return new ApplicationMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("applications", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("applications", $this->data->toArray());
      return (int) DB::insertId();
    } else {
      DB::update("applications", $this->data->toArray(), "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'gender',
      'year',
      'q1',
      'q2',
      'q3',
      'q4',
      'q5',
      'q6',
      'q7',
      'user_id',
      'status',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setGender(string $value): this {
    $this->data["gender"] = $value;
    return $this;
  }

  public function setYear(string $value): this {
    $this->data["year"] = $value;
    return $this;
  }

  public function setQuestion1(string $value): this {
    $this->data["q1"] = $value;
    return $this;
  }

  public function setQuestion2(string $value): this {
    $this->data["q2"] = $value;
    return $this;
  }

  public function setQuestion3(string $value): this {
    $this->data["q3"] = $value;
    return $this;
  }

  public function setQuestion4(string $value): this {
    $this->data["q4"] = $value;
    return $this;
  }

  public function setQuestion5(string $value): this {
    $this->data["q5"] = $value;
    return $this;
  }

  public function setQuestion6(string $value): this {
    $this->data["q6"] = $value;
    return $this;
  }

  public function setQuestion7(string $value): this {
    $this->data["q7"] = $value;
    return $this;
  }

  public function setUserID(int $value): this {
    $this->data["user_id"] = $value;
    return $this;
  }

  public function setStatus(int $value): this {
    $this->data["status"] = $value;
    return $this;
  }

  /* BEGIN MANUAL SECTION ApplicationMutator_footer */
  public static function deleteAppAndFeedback(int $user_id): void {
    DB::delete("feedback", "user_id=%s", $user_id);
    DB::delete("applications", "user_id=%s", $user_id);
  }

  public static function upsert(
    int $user_id,
    string $gender,
    string $year,
    string $q1,
    string $q2,
    string $q3,
    string $q4,
    string $q5,
    string $q6,
    string $q7,
  ): ?Application {
    # Make sure the user doesn't already have an application active
    $query =
      DB::query("SELECT * FROM applications WHERE user_id=%s", $user_id);

    if (DB::count() != 0) {
      # The user has submitted their app, don't allow them to update
      if ($query['submitted']) {
        Flash::set('error', 'You have already submitted an application');
        Route::redirect(
          MemberProfileController::getPrePath().Session::getUser()->getID(),
        );
      }

      # An application exists, just update it
      $paramData = Map {
        'gender' => $gender,
        'year' => $year,
        'q1' => $q1,
        'q2' => $q2,
        'q3' => $q3,
        'q4' => $q4,
        'q5' => $q5,
        'q6' => $q6,
        'q7' => $q7,
      };
      DB::update(
        'applications',
        $paramData->toArray(),
        'user_id=%s',
        $user_id,
      );
    } else {
      # Insert the application
      $paramData = Map {
        'user_id' => $user_id,
        'gender' => $gender,
        'year' => $year,
        'q1' => $q1,
        'q2' => $q2,
        'q3' => $q3,
        'q4' => $q4,
        'q5' => $q5,
        'q6' => $q6,
        'q7' => $q7,
        'status' => ApplicationState::NotStarted,
      };
      DB::insert('applications', $paramData->toArray());
    }

    return Application::loadByUser($user_id);
  }
  /* END MANUAL SECTION */
}
