<?hh // strict

class ApplicationSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'Gender' => ModelField::string_field('gender'),
      'Year' => ModelField::string_field('year'),
      'Question1' => ModelField::string_field('q1'),
      'Question2' => ModelField::string_field('q2'),
      'Question3' => ModelField::string_field('q3'),
      'Question4' => ModelField::string_field('q4'),
      'Question5' => ModelField::string_field('q5'),
      'Question6' => ModelField::string_field('q6'),
      'Question7' => ModelField::string_field('q7'),
      'UserID' => ModelField::int_field('user_id'),
      'Status' => ModelField::int_field('status'),
    };
  }

  public function getTableName(): string {
    return 'applications';
  }

  public function getIdField(): string {
    return 'id';
  }
}
