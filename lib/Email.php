<?hh //decl

class Email {
  public static Mailgun $mg;
  public static string $domain = 'example.com';
  public static string $from = 'hello@example.com';

  public static function send(
    string $list,
    string $subject,
    string $body
  ): void {
    self::$mg->sendMessage(self::$domain, array(
      'from' => self::$from,
      'to' => $list,
      'subject' => $subject,
      'html' => self::getHtmlMessage($body)
    ));
  }

  private static function getHtmlMessage(string $message): string {
    return '<body style="background: #277FB2 url(\'http://www.texaslan.org/img/bg.png\') repeat;font-family: sans-serif;">
<div class="email_body" style="margin: auto; max-width: 560px;">
  <div class="head_logo" style="padding-top: 30px; text-align: center;margin: 30px;">
    <img style="width: 60%;" src="http://www.texaslan.org/img/crest.png">
  </div>
  <div class="email_content" style="background: #fafcff;box-shadow: inset 1px 0px  #e3eaf1, 1px 0px #e3eaf1; border-bottom: 2px solid #d1ddf2;padding-left: 5%; padding-right: 5%; padding-top: 15px;">
    <div class="email_text">
      <p style="font-size: 16px;font-weight: 300;line-height: 27px;color: #161b21;">' . $message . '</p>
      <p style="font-size: 16px;font-weight: 300;line-height: 27px;color: #161b21;">Cheers,<br/>Lambda Alpha Nu Team</p>
    </div>
  </div>

</div>
<div class="email_footer" style="text-align: center;margin: 30px 0;">
  <p style="color: #e3e8ed;font-size: 12px;">Like us on <a href="https://www.facebook.com/texaslambdaalphanu" style="font-size: 11px;color: #e3e8ed;font-weight: 300;">Facebook!</a></p>
</div>
</body>';
  }

  public static function subscribe(string $list, User $user): void {
    self::$mg->post('lists/' . $list . '/members' , array(
      'address' => $user->getEmail(),
      'name' => $user->getFirstName() . ' ' . $user->getLastName(),
      'subscribed' => true
    ));
  }

  public static function getLists(): array {
    $result = self::$mg->get('lists');
    return $result->http_response_body->items;
  }
}
