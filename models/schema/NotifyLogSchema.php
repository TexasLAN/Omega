<?hh // strict

class NotifyLogSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'NotifyTitle' => ModelField::string_field('notify_title'),
      'NotifyText' => ModelField::string_field('notify_text'),
      'SenderUserId' => ModelField::int_field('sender_user_id'),
      'SentTime' => ModelField::date_field('sent_time'),
      'DefaultFooter' => ModelField::bool_field('default_footer'),
      'HtmlParsed' => ModelField::bool_field('html_parsed'),
    };
  }

  public function getTableName(): string {
    return 'notify_log';
  }

  public function getIdField(): string {
    return 'id';
  }
}
