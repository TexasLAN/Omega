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
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>When</th>
          <th>Actions</th>
        </tr>
      </table>;

    $events = Event::genAllFuture();
    foreach($events as $event) {
      $stringID = (string) $event->getID();
      $upcoming_events->appendChild(
        <tr>
          <td><a href={'/events/' . $event->getID()}>{$event->getID()}</a></td>
          <td>{$event->getName()}</td>
          <td>{$event->getLocation()}</td>
          <td>{$event->getDatetime()}</td>
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
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>When</th>
          <th>Actions</th>
        </tr>
      </table>;

    $events = Event::genAllPast();
    foreach($events as $event) {
      $past_events->appendChild(
        <tr>
          <td>{$event->getID()}</td>
          <td>{$event->getName()}</td>
          <td>{$event->getLocation()}</td>
          <td>{$event->getDatetime()}</td>
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
                <label>Date</label>
                <input type="date" class="form-control" name="date" />
              </div>
              <div class="form-group">
                <label>Time</label>
                <input type="time" class="form-control" name="time" />
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
      </div>;
  }

  public static function post(): void {
    # We're deleting an event
    if(isset($_POST['delete'])) {
      Event::deleteByID((int)$_POST['delete']);
      Flash::set('success', 'Event deleted successfully');
      Route::redirect('/events/admin');
    }

    # All fields must be present
    if(!isset($_POST['name']) ||
       !isset($_POST['location']) ||
       !isset($_POST['date']) ||
       !isset($_POST['time'])) {
      Flash::set('error', 'All fields must be filled out');
      Route::redirect('/events/admin');
    }

    Event::create($_POST['name'], $_POST['location'], $_POST['date'], $_POST['time']);
    Flash::set('success', 'Event created successfully');
    Route::redirect('/events/admin');
  }
}
