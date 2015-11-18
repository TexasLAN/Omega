<?hh // strict

class UserSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'Email' => ModelField::string_field('email'),
      'FirstName' => ModelField::string_field('fname'),
      'LastName' => ModelField::string_field('lname'),
      'Username' => ModelField::string_field('username'),
      'Password' => ModelField::string_field('password'),
      'MemberStatus' => ModelField::int_field('member_status'),
      'Token' => ModelField::string_field('token')->optional(),
      'ForgotToken' => ModelField::string_field('forgot_token')->optional()
    };
  }

  public function getTableName(): string {
    return 'users';
  }

  public function getIdField(): string {
    return 'id';
  }
}