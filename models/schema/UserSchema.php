<?hh // strict

class UserSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'Email' => ModelField::string_field('email'),
      'FullName' => ModelField::string_field('full_name'),
      'NickName' => ModelField::string_field('nick_name'),
      'Username' => ModelField::string_field('username'),
      'Password' => ModelField::string_field('password'),
      'MemberStatus' => ModelField::int_field('member_status'),
      'PhoneNumber' => ModelField::string_field('phone_number'),
      'GraduationYear' => ModelField::int_field('grad_year'),
      'HasVoted' => ModelField::bool_field('has_voted'),
      'Class' => ModelField::int_field('class')->optional(),
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