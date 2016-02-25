<?hh // strict

class ReviewSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'Comments' => ModelField::string_field('comments'),
      'Rating' => ModelField::int_field('rating'),
      'UserID' => ModelField::int_field('user_id'),
      'AppID' => ModelField::int_field('application_id'),
    };
  }

  public function getTableName(): string {
    return 'reviews';
  }

  public function getIdField(): string {
    return 'id';
  }
}
