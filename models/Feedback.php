<?hh

class Feedback {

  private string $comments = '';

  public static function upsert(
    string $comments,
    int $user_id,
    int $reviewer_id
  ): void {
    DB::query("SELECT * FROM feedback WHERE user_id=%s AND reviewer_id=%s", $user_id, $reviewer_id);

    if(DB::count() != 0) {
      $paramData = Map {
        'comments' => $comments
      };
      DB::update('feedback', $paramData->toArray(), 'user_id=%s AND reviewer_id=%s', $user_id, $reviewer_id);
    } else {
      $paramData = Map {
        'comments' => $comments,
        'user_id' => $user_id,
        'reviewer_id' => $reviewer_id
      };
      DB::insert('feedback', $paramData->toArray());
    }
  }

  public function getComments(): string {
    return $this->comments;
  }

  public function gen($user_id, $reviewer_id): Feedback {
    $query = DB::queryFirstRow("SELECT * FROM feedback WHERE user_id=%s AND reviewer_id=%s", $user_id, $reviewer_id);
    if(!$query) {
      return new Feedback();
    }

    $feedback = new Feedback();
    $feedback->comments = $query['comments'];
    return $feedback;
  }
}
