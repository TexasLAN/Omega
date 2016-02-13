<?hh

final class :omega:head extends :x:element {
  attribute ?string title;

  final protected function render(): :head {
    $title = "Omega | Texas LAN";
    if(!is_null($this->:title)) {
      $title = $this->:title . " | Texas LAN";
    }

    return
      <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{$title}</title>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/styles.css" />
        <link rel="icon" type="image/png" href="/img/favicon.png" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
      </head>;
  }
}
