<?hh //decl

class Venmo {
	public static Map<string, mixed> $venmoData;
	public static string $api_url = 'https://api.venmo.com/v1';
	public static string $scopes = 'make_payments';
	public static string $client_id = '';
	public static string $client_secret = '';
  public static int $lan_dues = 0;

  // public static function getAuthURL(): string {
  //   $csrf_token = md5(uniqid(rand(), true)); //CSRF style token should be saved to implement CSRF check
    
  //   $fields = array(
  //       'client_id' => Venmo::$client_id,
  //       'scope' => Venmo::$scopes,
  //       'response_type' => 'code',
  //       'redirect_uri' => 'https://omega.texaslan.org' . MemberSettingsController::getPath(),
  //       'state' => $csrf_token
  //   );
    
  //   return Venmo::$api_url . '/oauth/authorize?' . http_build_query($fields);
  // }

  // public static function exchangeToken($authorization_code): string {
  //   $url = Venmo::$api_url . '/oauth/access_token';
  //   $fields = array(
  //       'client_id' => Venmo::$client_id,
  //       'client_secret' => Venmo::$client_secret,
  //       'code' => $authorization_code
  //   ); 
  //   $response = self::curlMethod($url, $fields);
  //   return $response;
  // }

  private static function getAccessToken(): string {
    $curDatetime = new DateTime(date(DateTime::ISO8601));
    $refresh_date = self::strToDatetime(Settings::get('venmo_duration'));

    if($curDatetime < $refresh_date) {
      return Settings::get('venmo_token');
    } else {
      return refreshAccessToken();
    }
  }

  private static function strToDatetime(string $date): DateTime {
    $datetime = DateTime::createFromFormat(DateTime::ISO8601, $date);
    return $datetime;
  }

  private static function datetimeToStr(DateTime $date): string {
    return $date->format(DateTime::ISO8601);
  }

  /*
   * Access Tokens eventually expire, but can be renewed easily using this method
   */
  private static function refreshAccessToken(): string {
    $url = Venmo::$api_url . '/oauth/access_token';
    $fields = array(
        'client_id' => Venmo::$client_id,
        'client_secret' => Venmo::$client_secret,
        'refresh_token' => Settings::get('venmo_refresh')
    ); 
    $access_obj = self::curlMethod($url,$fields);

    $durationEndTime = new DateTime(date(DateTime::ISO8601));
    date_add($durationEndTime, DateInterval::createFromDateString(json_decode($access_obj)->expires_in . ' seconds'));

    Settings::set('venmo_token', json_decode($access_obj)->access_token);
    Settings::set('venmo_refresh', json_decode($access_obj)->refresh_token);
    Settings::set('venmo_duration', self::datetimeToStr($durationEndTime));
    return json_decode($access_obj)->access_token;
  }

  // public static function sendPayment($pay_to_phone, $amount, $note): string {
  //   if($amount < 0) return 'ERROR';

  //   $url = Venmo::$api_url . '/payments';
  //   $fields = array(
  //       'access_token' => self::getAccessToken(),
  //       'phone' => $pay_to_phone,
  //       'amount' => $amount,
  //       'note' => $note . ' via Omega',
  //       'audience' => 'private'
  //   );
    
  //   $response = self::curlMethod($url, $fields);
    
  //   return $response;
  // }

  public static function requestPayment($request_to_phone, $amount, $note): string {
    if($amount < 0) return 'ERROR';
    $amount = $amount * -1;

    $url = Venmo::$api_url . '/payments';
    $fields = array(
        'access_token' => self::getAccessToken(),
        'phone' => $request_to_phone,
        'amount' => $amount,
        'note' => $note . ' via Omega',
        'audience' => 'private'
    );
    
    $response = self::curlMethod($url, $fields);
    
    return $response;
  }


  private static function curlMethod($url, $fields): string {
    // Open connection
    $ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    // Close connection
    curl_close($ch);
    return $result;
  }
}