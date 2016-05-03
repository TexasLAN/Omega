<?hh

class Render {
  public static function go(:xhp $content, ?string $controller, ?string $title): void {
    $user = null;
    if (Session::isActive()) {
      $user = Session::getUser();
    }

    print
      <x:frag>
        <omega:head title={$title}/>
        <body>
          <omega:nav-bar user={$user} controller={$controller} />
          <div class="container">
            {self::getFlash()}
            {$content}
          </div>
        </body>
      </x:frag>
    ;
  }

  private static function getFlash(): ?:div {
    if (Flash::exists('error')) {
      return
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          {Flash::get('error')}
        </div>;
    } else if (Flash::exists('success')) {
      return
        <div class="alert alert-success alert-dismissible" role="alert">
          <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          {Flash::get('success')}
        </div>;
    }
    return null;
  }
}
