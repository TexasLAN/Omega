<?hh

class ExampleUnsafeRenderable implements XHPUnsafeRenderable {
  public function __construct(public string $htmlString) {}
  public function toHTMLString() {
    return $this->htmlString;
  }
}
//, XHPAlwaysValidChild
class ExampleVeryUnsafeRenderable extends ExampleUnsafeRenderable
implements XHPUnsafeRenderable {
}

final class :omega:email-message extends :x:element {
  attribute ?string message;

  final protected function render(): :div {
    $message = $this->:message;
    if(is_null($message)) {
      $message = '';
    }
    $message_xhp = new ExampleUnsafeRenderable($message);
    return
      <div>
        {$message_xhp}
      </div>;
  }
}
