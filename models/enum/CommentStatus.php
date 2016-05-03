<?hh

enum CommentStatus : int as int {
  Open = 0;
  Closed = 1;
}

class CommentStatusInfo {
  public static function getName(int $value): string {
    switch ($value) {
      case CommentStatus::Open:
        return 'Open';
      case CommentStatus::Closed:
        return 'Closed';
      default:
        return '';
    }
  }
}
