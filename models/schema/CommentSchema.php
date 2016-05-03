<?hh // strict

class CommentSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'Message' => ModelField::string_field('message'),
      'Status' => ModelField::int_field('status'),
    };
  }

  public function getTableName(): string {
    return 'comment';
  }

  public function getIdField(): string {
    return 'id';
  }
}
