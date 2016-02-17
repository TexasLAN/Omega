<?hh //decl

class Email {
  public static SendGrid $sendgrid;
  public static string $domain = 'example.com';
  public static string $from = 'hello@example.com';
  public static string $webmaster_test = 'hello@example.com';

  public static function send(
    array $toList,
    string $subject,
    string $body,
    bool $default_footer,
    bool $htmlParser
  ): void {
    $email = new SendGrid\Email();
    $email
      ->setSmtpapiTos($toList)
      ->setFrom(self::$from)
      ->setSubject($subject)
      ->setHtml(self::getHtmlMessage($body, $default_footer, $htmlParser));
    self::$sendgrid->send($email);
  }

  private static function getHtmlMessage(string $message, bool $default_footer, bool $htmlParser): string {
    $parsed_message = $message;
    if (!$htmlParser) {
      $Parsedown = new Parsedown();
      $parsed_message = $Parsedown->text($message);
    }
    $footer = ($default_footer) ? '<p style="font-size: 16px;font-weight: 300;line-height: 27px;color: #161b21;">Cheers,<br/>Lambda Alpha Nu Team</p>' : ' ';
    return '<body style="background: #277FB2 url(\'http://www.texaslan.org/img/bg.png\') repeat;font-family: sans-serif;">
<div class="email_body" style="margin: auto; max-width: 560px;">
  <div class="head_logo" style="padding-top: 30px; text-align: center;margin: 30px;">
    <img style="width: 60%;" src="http://www.texaslan.org/img/crest.png"/>
  </div>
  <div class="email_content" style="background: #fafcff;box-shadow: inset 1px 0px  #e3eaf1, 1px 0px #e3eaf1; border-bottom: 2px solid #d1ddf2;padding-left: 5%; padding-right: 5%; padding-top: 15px;">
    <div class="email_text">
      <p style="font-size: 16px;font-weight: 300;line-height: 27px;color: #161b21;">' . $parsed_message . '</p>' . $footer .
    '</div>
  </div>

</div>
<div class="email_footer" style="text-align: center;margin: 30px 0;">
  <p style="color: #e3e8ed;font-size: 12px;">Like us on <a href="https://www.facebook.com/texaslambdaalphanu" style="font-size: 11px;color: #e3e8ed;font-weight: 300;">Facebook!</a></p>
</div>
</body>';
  }

  public static function getXhpMessage(string $message, bool $default_footer, bool $htmlParser ): xhp_div {
    $parsed_message = $message;
    if (!$htmlParser) {
      $Parsedown = new Parsedown();
      $parsed_message = $Parsedown->text($message);
    }
    $footer = ($default_footer) ? <p style="font-size: 16px;font-weight: 300;line-height: 27px;color: #161b21;">Cheers,<br/>Lambda Alpha Nu Team</p> : <p />;
    return <div style="background: #277FB2 url(http://www.texaslan.org/img/bg.png) repeat;font-family: sans-serif;">
  <div class="email_body" style="margin: auto; max-width: 560px;">
    <div class="head_logo" style="padding-top: 30px; text-align: center;margin: 30px;">
      <img style="width: 60%;" src="http://www.texaslan.org/img/crest.png"/>
    </div>
    <div class="email_content" style="background: #fafcff;box-shadow: inset 1px 0px  #e3eaf1, 1px 0px #e3eaf1; border-bottom: 2px solid #d1ddf2;padding-left: 5%; padding-right: 5%; padding-top: 15px;">
      <div class="email_text">
        <omega:email-message message={$parsed_message} />
        {$footer}
      </div>
    </div>

  </div>
  <div class="email_footer" style="text-align: center;margin: 30px 0;">
    <p style="color: #e3e8ed;font-size: 12px;">Like us on <a href="https://www.facebook.com/texaslambdaalphanu" style="font-size: 11px;color: #e3e8ed;font-weight: 300;">Facebook!</a></p>
  </div>
  </div>;
  }

  public static function getEmailList(string $emailListStr, ?UserState $userState): array {
    $emailList = array();
    if(!strcmp($_POST['email'], 'Webmaster Test')) {
      array_push($emailList, Email::$webmaster_test);
    } else {
      if(!is_null($userState)) {
        $emailList = self::getUserStateEmailList($userState);
      } else {
        array_push($emailList, Email::$webmaster_test);
      }
    }

    return $emailList;
  }

  private static function getUserStateEmailList(UserState $state): array {
    $userList = User::loadStates(Vector {$state});

    $emailList = array();
    foreach($userList as $row_user) {
      array_push($emailList, $row_user->getEmail());
    }
      
    return $emailList;
  }
}
