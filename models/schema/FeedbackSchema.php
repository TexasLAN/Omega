<?hh // strict

class FeedbackSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'Comments' => ModelField::string_field('comments'),
      'UserID' => ModelField::int_field('user_id'),
      'ReviewerID' => ModelField::int_field('reviewer_id'),
    };
  }

  public function getTableName(): string {
    return 'feedback';
  }

  public function getIdField(): string {
    return '';
  }
}
