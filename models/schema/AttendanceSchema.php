<?hh // strict

class AttendanceSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'UserID' => ModelField::int_field('user_id'),
      'EventID' => ModelField::int_field('event_id'),
      'Status' => ModelField::int_field('status')
    };
  }

  public function getTableName(): string {
    return 'attendance';
  }

  public function getIdField(): string {
    return '';
  }
}