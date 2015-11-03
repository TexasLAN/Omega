<?hh

class EventsAdminController extends BaseController {
  public static function getPath(): string {
    return '/events/admin';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Member
        });
    $newConfig->setUserRoles(
      Vector {
        UserRoleEnum::Officer,
        UserRoleEnum::Admin
        });
    $newConfig->setTitle('Events Admin');
    return $newConfig;
  }

  public static function get(): :xhp {
    # Generate a table of all future events
    $upcoming_events =
      <table class="table table-bordered table-striped sortable">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>When</th>
          <th data-defaultsort="disabled">Actions</th>
        </tr>
      </table>;

    $events = Event::loadFuture();
    foreach($events as $event) {
      $stringID = (string) $event->getID();
      $upcoming_events->appendChild(
        <tr>
          <td><a href={'/events/' . $event->getID()}>{$event->getID()}</a></td>
          <td>{$event->getName()}</td>
          <td>{$event->getLocation()}</td>
          <td>{Event::datetimeToStr($event->getStartDate())}</td>
          <td>
            <form method="post" action="/events/admin">
              <button name="delete" class="btn btn-danger" value={$stringID} type="submit">
                Delete
              </button>
            </form>
          </td>
        </tr>
      );
    }

    $past_events =
      <table class="table table-bordered table-striped sortable">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>When</th>
          <th data-defaultsort="disabled">Actions</th>
        </tr>
      </table>;

    $events = Event::loadPast();
    foreach($events as $event) {
      $past_events->appendChild(
        <tr>
          <td>{$event->getID()}</td>
          <td>{$event->getName()}</td>
          <td>{$event->getLocation()}</td>
          <td>{Event::datetimeToStr($event->getStartDate())}</td>
          <td>
            <a href={'/events/attendance/' . $event->getID()} class="btn btn-primary">
              View Attendance
            </a>
          </td>
        </tr>
      );
    }

    return
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Create New Event</h1>
          </div>
          <div class="panel-body">
            <form method="post" action="/events/admin">
              <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" />
              </div>
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location" />
              </div>
              <div class="form-group">
                <label>Start Date</label>
                <input type="date" class="form-control" name="start_date" />
              </div>
              <div class="form-group">
                <label>Start Time</label>
                <input type="time" class="form-control" name="start_time" />
              </div>
              <div class="form-group">
                <label>End Date</label>
                <input type="date" class="form-control" name="end_date" />
              </div>
              <div class="form-group">
                <label>End Time</label>
                <input type="time" class="form-control" name="end_time" />
              </div>
              <button type="submit" name="create" value="1" class="btn btn-default">Submit</button>
            </form>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Upcoming Events</h1>
          </div>
          <div class="panel-body">
            {$upcoming_events}
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Past Events</h1>
          </div>
          <div class="panel-body">
            {$past_events}
          </div>
        </div>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  public static function post(): void {
    # We're deleting an event
    if(isset($_POST['delete'])) {
      EventMutator::delete((int)$_POST['delete']);
      Flash::set('success', 'Event deleted successfully');
      Route::redirect('/events/admin');
    }

    # All fields must be present
    if(!isset($_POST['name']) ||
       !isset($_POST['location']) ||
       !isset($_POST['start_date']) ||
       !isset($_POST['start_time']) ||
       !isset($_POST['end_date']) ||
       !isset($_POST['end_time'])) {
      Flash::set('error', 'All fields must be filled out');
      Route::redirect('/events/admin');
    }
    EventMutator::create()
      ->setName($_POST['name'])
      ->setLocation($_POST['location'])
      ->setStartDate(Event::strToDatetime($_POST['start_date'], $_POST['start_time']))
      ->setEndDate(Event::strToDatetime($_POST['end_date'], $_POST['end_time']))
      ->save();
    // Event::create($_POST['name'], $_POST['location'], $_POST['date'], $_POST['time']);
    Flash::set('success', 'Event created successfully');
    Route::redirect('/events/admin');
  }
}
