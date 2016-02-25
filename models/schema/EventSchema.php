<?hh // strict

class EventSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'Name' => ModelField::string_field('name'),
      'Location' => ModelField::string_field('location'),
      'StartDate' => ModelField::date_field('start_date'),
      'EndDate' => ModelField::date_field('end_date'),
      'Type' => ModelField::string_field('type'),
      'Description' => ModelField::string_field('description'),
    };
  }

  public function getTableName(): string {
    return 'events';
  }

  public function getIdField(): string {
    return 'id';
  }
}
