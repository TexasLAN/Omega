<?hh

class VenmoController extends BaseController {
  public static function getPath(): string {
    return '/venmo';
  }

  public static function get(): :xhp {

    return
      <div class="col-md-6 col-md-offset-3 masthead">
        <div id="crest"></div>
      </div>;
  }
}
