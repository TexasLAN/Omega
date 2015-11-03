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
              <button
                type="button"
                class="btn btn-primary"
                data-toggle="modal"
                data-target="#eventMutator"
                data-type="update"
                data-id={(string) $event->getID()}
                data-name={$event->getName()}
                data-location={$event->getLocation()}
                data-startdate={(string) Event::datetimeToWeb($event->getStartDate())}
                data-enddate={(string) Event::datetimeToWeb($event->getEndDate())}>
                Update
              </button>
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
            <button
                type="button"
                class="btn btn-primary"
                data-toggle="modal"
                data-target="#eventMutator"
                data-type="create"
                data-id=""
                data-name=""
                data-location=""
                data-startdate=""
                data-enddate="">
                New Event
              </button>
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
        {self::getEventModal()}
        <script src="/js/eventsAdmin.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getEventModal(): :xhp {
    $form = <form action="/events/admin" method="post" />;
    $formChildren = <div><div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" id="name" />
              </div>
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location" id="location" />
              </div>
              <div class="form-group">
                <label>Start Date</label>
                <input type="date" class="form-control" name="start_date" id="start_date" />
              </div>
              <div class="form-group">
                <label>Start Time</label>
                <input type="time" class="form-control" name="start_time" id="start_time" />
              </div>
              <div class="form-group">
                <label>End Date</label>
                <input type="date" class="form-control" name="end_date" id="end_date" />
              </div>
              <div class="form-group">
                <label>End Time</label>
                <input type="time" class="form-control" name="end_time" id="end_time" />
              </div>
              </div>;
    $form->appendChild($formChildren);
    $form->appendChild(
      <input type="hidden" name="event_mutator" />
    );
    $form->appendChild(
      <input type="hidden" name="type" id="type"/>
    );
    $form->appendChild(
      <input type="hidden" name="id" id="id"/>
    );
    return
      <div class="modal fade" id="eventMutator" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="eventName" />
            </div>
            <div class="modal-body">
              {$form}
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="submit">Save</button>
            </div>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    error_log("1");
    // We're deleting an event
    if(isset($_POST['delete'])) {
      EventMutator::delete((int)$_POST['delete']);
      Flash::set('success', 'Event deleted successfully');
      Route::redirect('/events/admin');
    } elseif (isset($_POST['event_mutator'])) {
      error_log("2");
      error_log($_POST['type']);
      // All fields must be present
      if(!isset($_POST['name']) ||
         !isset($_POST['location']) ||
         !isset($_POST['start_date']) ||
         !isset($_POST['start_time']) ||
         !isset($_POST['end_date']) ||
         !isset($_POST['end_time'])) {
        Flash::set('error', 'All fields must be filled out');
        Route::redirect('/events/admin');
      }
      if($_POST['type'] == 'create') {
        error_log("3.0");
        EventMutator::create()
        ->setName($_POST['name'])
        ->setLocation($_POST['location'])
        ->_setStartDate(Event::strToDatetime($_POST['start_date'], $_POST['start_time']))
        ->_setEndDate(Event::strToDatetime($_POST['end_date'], $_POST['end_time']))
        ->save();
        Flash::set('success', 'Event created successfully');
      } elseif($_POST['type'] == 'update') {
        error_log("3.1");
          EventMutator::update((int) $_POST['id'])
          ->setName($_POST['name'])
          ->setLocation($_POST['location'])
          ->_setStartDate(Event::strToDatetime($_POST['start_date'], $_POST['start_time']))
          ->_setEndDate(Event::strToDatetime($_POST['end_date'], $_POST['end_time']))
          ->save();
        Flash::set('success', 'Event updated successfully');
      }
      Route::redirect('/events/admin');
    }
  }
}
