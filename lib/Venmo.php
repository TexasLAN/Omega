<?hh //decl

class Venmo {
	public static Map<string, mixed> $venmoData;
	public static string $api_url = 'https://api.venmo.com/v1';
	public static string $scopes = 'access_email access_phone make_payments';
	public static string $client_id = '';
	public static string $client_secret = '';

  public function getAuthURL(): string {
    $csrf_token = md5(uniqid(rand(), true)); //CSRF style token should be saved to implement CSRF check
    
    $fields = array(
        'client_id' => $client_id,
        'scope' => $scopes,
        'response_type' => 'code', //required for server side token exchange
        'state' => $csrf_token
    );
    
    return $venmoData['api_url'] . '/oauth/authorize?' . http_build_query($fields);
  }

  public function exchangeToken($authorization_code): string {
    //make sure client send authorization code, something like
    //$authorization_code = $_GET["code"];
    
    $url = $api_url . '/oauth/access_token';
    $fields = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $authorization_code
    ); 
    $response = $this->curlMethod($url,$fields);
    return $response;
  }

  private function curlMethod($url,$fields): string {
    // Open connection
    $ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
    
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