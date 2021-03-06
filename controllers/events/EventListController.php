<?hh

class EventsListController extends BaseController {
  public static function getPath(): string {
    return '/event';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Pledge, UserState::Active});
    $newConfig->setTitle('Events');
    return $newConfig;
  }

  private static function validateActions($user): bool {
    return
      $user->validateRole(UserRoleEnum::Officer) ||
      $user->validateRole(UserRoleEnum::Admin);
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    // Generate a table of all the actions for the event list controller
    $action_panel = <div />;
    if (self::validateActions($user)) {
      $action_panel =
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Actions</h1>
          </div>
          <div class="panel-body btn-toolbar">
            <button
              type="button"
              class="btn btn-primary"
              data-toggle="modal"
              data-target="#eventMutator"
              data-method="create">
              New Event
            </button>
          </div>
        </div>;
    }

    // Generate a table of all future events
    $upcoming_events =
      <table class="table table-bordered table-striped sortable">
        <tr>
          <th>Name</th>
          <th>Type</th>
          <th>Location</th>
          <th>When</th>
          <th data-defaultsort="disabled">Actions</th>
        </tr>
      </table>;

    $events = Event::loadFuture();
    foreach ($events as $event) {
      $stringID = (string) $event->getID();
      $upcoming_event_actions =
        <form
          class="btn-toolbar"
          method="post"
          action={EventsListController::getPath()}>
          <a
            href={EventDetailsController::getPrePath().$event->getID()}
            class="btn btn-primary">
            View Details
          </a>
        </form>;

      if (self::validateActions($user)) {
        $upcoming_event_actions->appendChild(
          <button
            type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#eventMutator"
            data-method="update"
            data-type={$event->getType()}
            data-id={(string) $event->getID()}
            data-name={$event->getName()}
            data-location={$event->getLocation()}
            data-startdate={$event->getStartDateWeb()}
            data-enddate={$event->getEndDateWeb()}
            data-description={$event->getDescription()}
            data-neededpoints={(string) $event->getNeededPoints()}>
            Update
          </button>
        );
        $upcoming_event_actions->appendChild(
          <button
            name="delete"
            class="btn btn-danger"
            value={$stringID}
            type="submit">
            Delete
          </button>
        );
      }

      $upcoming_events->appendChild(
        <tr>
          <td>{$event->getName()}</td>
          <td>{EventTypeInfo::getEventTypeName($event->getType())}</td>
          <td>{$event->getLocation()}</td>
          <td>{$event->getStartDateStr()}</td>
          <td>{$upcoming_event_actions}</td>
        </tr>
      );
    }

    // Generate Table of past events
    $past_events =
      <table class="table table-bordered table-striped sortable">
        <tr>
          <th>Name</th>
          <th>Type</th>
          <th>Location</th>
          <th>When</th>
          <th data-defaultsort="disabled">Actions</th>
        </tr>
      </table>;

    $events = Event::loadPast();
    foreach ($events as $event) {
      $stringID = (string) $event->getID();
      $past_events_actions =
        <form
          class="btn-toolbar"
          method="post"
          action={EventsListController::getPath()}>
          <a
            href={EventDetailsController::getPrePath().$event->getID()}
            class="btn btn-primary">
            View Details
          </a>
        </form>;
      if (self::validateActions($user)) {
        $past_events_actions->appendChild(
          <button
            type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#eventMutator"
            data-method="update"
            data-type={$event->getType()}
            data-id={(string) $event->getID()}
            data-name={$event->getName()}
            data-location={$event->getLocation()}
            data-startdate={$event->getStartDateWeb()}
            data-enddate={$event->getEndDateWeb()}
            data-description={$event->getDescription()}>
            Update
          </button>
        );
      }

      $past_events->appendChild(
        <tr>
          <td>{$event->getName()}</td>
          <td>{EventTypeInfo::getEventTypeName($event->getType())}</td>
          <td>{$event->getLocation()}</td>
          <td>{$event->getStartDateStr()}</td>
          <td>{$past_events_actions}</td>
        </tr>
      );
    }

    return
      <div class="col-md-12">
        {$action_panel}
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
    $eventTypeSelect = <select id="type" name="type"></select>;
    foreach (EventType::getValues() as $name => $value) {
      $eventTypeSelect->appendChild(
          <option value={(string) $value} selected={true}>{EventTypeInfo::getEventTypeName($value)}</option>,
        );
    }

    return
      <div
        class="modal fade"
        id="eventMutator"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <h3 class="modal-title" id="eventName" />
            </div>
            <div class="modal-body">
              <form action={self::getPath()} method="post">
                <div>
                  <div class="form-group">
                    <label>Name</label>
                    <input
                      type="text"
                      class="form-control"
                      name="name"
                      id="name"
                    />
                  </div>
                  <div class="form-group">
                    <label>Location</label>
                    <input
                      type="text"
                      class="form-control"
                      name="location"
                      id="location"
                    />
                  </div>
                  <div class="form-group">
                    <label>Type</label><br />
                    {$eventTypeSelect}
                  </div>
                  <div class="form-group">
                    <label>Needed Points</label>
                    <input
                      type="number"
                      class="form-control"
                      name="needed_points"
                      id="needed_points"
                    />
                  </div>
                  <div class="form-group">
                    <label>Start Date</label>
                    <input
                      type="date"
                      class="form-control"
                      name="start_date"
                      id="start_date"
                    />
                  </div>
                  <div class="form-group">
                    <label>Start Time</label>
                    <input
                      type="time"
                      class="form-control"
                      name="start_time"
                      id="start_time"
                    />
                  </div>
                  <div class="form-group">
                    <label>End Date</label>
                    <input
                      type="date"
                      class="form-control"
                      name="end_date"
                      id="end_date"
                    />
                  </div>
                  <div class="form-group">
                    <label>End Time</label>
                    <input
                      type="time"
                      class="form-control"
                      name="end_time"
                      id="end_time"
                    />
                  </div>
                  <div class="form-group">
                    <label>Description</label>
                  </div>
                  <div class="form-group">
                    <textarea
                      class="fixed-textarea"
                      name="description"
                      id="description"
                    />
                  </div>
                </div>
                <input type="hidden" name="event_mutator" />
                <input type="hidden" name="method" id="method" />
                <input type="hidden" name="id" id="id" />
              </form>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-default"
                data-dismiss="modal">
                Cancel
              </button>
              <button type="button" class="btn btn-primary" id="submit">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    // Validate user login
    $user = Session::getUser();
    if (!self::validateActions($user)) {
      Flash::set(
        'error',
        'You do not have the required roles to alter the information',
      );
      Route::redirect(self::getPath());
    }

    // We're deleting an event
    if (isset($_POST['delete'])) {
      // Delete all attendance records for the event
      AttendanceMutator::deleteEvent((int) $_POST['delete']);
      EventMutator::delete((int) $_POST['delete']);
      Flash::set('success', 'Event deleted successfully');
      Route::redirect(self::getPath());
    } else if (isset($_POST['event_mutator'])) {
      // All fields must be present
      if (!isset($_POST['name']) ||
          !isset($_POST['type']) ||
          !isset($_POST['location']) ||
          !isset($_POST['needed_points']) ||
          !isset($_POST['start_date']) ||
          !isset($_POST['start_time']) ||
          !isset($_POST['end_date']) ||
          !isset($_POST['end_time']) ||
          !isset($_POST['description'])) {
        Flash::set('error', 'All fields must be filled out');
        Route::redirect(self::getPath());
      }
      if ($_POST['method'] == 'create') {
        EventMutator::create()
          ->setName($_POST['name'])
          ->setLocation($_POST['location'])
          ->_setStartDate(
            Event::strToDatetime($_POST['start_date'], $_POST['start_time']),
          )
          ->_setEndDate(
            Event::strToDatetime($_POST['end_date'], $_POST['end_time']),
          )
          ->setType($_POST['type'])
          ->setDescription($_POST['description'])
          ->setNeededPoints((int) $_POST['needed_points'])
          ->save();
        Flash::set('success', 'Event created successfully');
        $createdEventID = Event::loadRecentCreated()->getID();

        $userList = array();

        switch ($_POST['type']) {
          case EventType::GeneralMeeting:
            $userList =
              User::loadStates(Vector {UserState::Pledge, UserState::Active});
            break;
          case EventType::OfficerMeeting:
            $userList = User::loadRole(UserRoleEnum::Officer);
            break;
        }

        foreach ($userList as $user) {
          AttendanceMutator::create()
            ->setUserID((int) $user->getID())
            ->setEventID((int) $createdEventID)
            ->setStatus(AttendanceState::NotPresent)
            ->save();
        }

      } else if ($_POST['method'] == 'update') {
        EventMutator::update((int) $_POST['id'])
          ->setName($_POST['name'])
          ->setLocation($_POST['location'])
          ->setType($_POST['type'])
          ->_setStartDate(
            Event::strToDatetime($_POST['start_date'], $_POST['start_time']),
          )
          ->_setEndDate(
            Event::strToDatetime($_POST['end_date'], $_POST['end_time']),
          )
          ->setDescription($_POST['description'])
          ->setNeededPoints((int) $_POST['needed_points'])
          ->save();
        Flash::set('success', 'Event updated successfully');
      }
      Route::redirect(self::getPath());
    }
  }
}
