<?hh

final class :omega:event-list extends :x:element {
  attribute
    array<Event> events = array();

  final protected function render(): :table {
    $events = $this->getAttribute('events');

    $event_list =
      <table class="table">
        <tr>
          <th>Name</th>
          <th>Location</th>
          <th>When</th>
        </tr>
      </table>;

    foreach($events as $event) {
      $event_list->appendChild(
        <tr>
          <td>{$event->getName()}</td>
          <td>{$event->getLocation()}</td>
          <td>{Event::datetimeToStr($event->getStartDate())}</td>
        </tr>
      );
    }

    return $event_list;
  }
}
