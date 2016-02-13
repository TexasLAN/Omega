<?hh //decl

class ReviewSingleController extends BaseController {
  public static function getPath(): string {
    return '/review/(?<id>\d+)';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setUserRoles(
      Vector {
        UserRoleEnum::Reviewer,
        UserRoleEnum::Admin
        });
    $newConfig->setTitle('Review');
    return $newConfig;
  }

  public static function get(): :xhp {
    $app_id = (int)$_SESSION['route_params']['id'];

    $application = Application::load((int)$app_id);
    $user = User::load($application->getUserID());
    if(is_null($user) || !$user->isReviewable()) {
      return FourOhFourController::get();
    }

    $review = Review::loadByUserAndApp(Session::getUser()->getID(), $application->getID());

    // Admins get special actions like delete and promote
    $admin_controls = null;
    if(Session::getUser()->validateRole(UserRoleEnum::Admin)) {
      $admin_controls =
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Admin Actions</h1>
          </div>
          <div class="panel-body">
            <form class="btn-toolbar" method="post" action={ReviewListController::getPath()}>
              <button name="candidate" class="btn btn-primary" value={(string) $user->getID()} type="submit">
                Promote to Candidate
              </button>
              <button name="delete" class="btn btn-danger" value={(string) $user->getID()} type="submit">
                Delete this application
              </button>
            </form>
          </div>
        </div>;
    }

    $email_hash = md5(strtolower(trim($user->getEmail())));
    $gravatar_url = 'https://secure.gravatar.com/avatar/' . $email_hash . '?s=200';

    $avg_rating = Review::getAvgRating($app_id);

    return
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1>{$user->getFullName()}</h1>
          </div>
          <div class="panel-body">
            <p class="text-center">
              <img src={$gravatar_url} class="img-thumbnail" />
            </p>
          </div>
          <table class="table">
            <tr>
              <th>Gender</th>
              <th>Year</th>
              <th>Email</th>
            </tr>
            <tr>
              <td>{$application->getGender()}</td>
              <td>{$application->getYear()}</td>
              <td>
                <a href={'mailto:' . $user->getEmail()} target="_blank">
                  {$user->getEmail()}
                </a>
              </td>
            </tr>
          </table>
          <div class="panel-body">
            <h4>Why do you want to rush Lambda Alpha Nu?</h4>
            <p>{$application->getQuestion1()}</p>
            <hr/>
            <h4>Talk about yourself in a couple of sentences.</h4>
            <p>{$application->getQuestion2()}</p>
            <hr/>
            <h4>What is your major and why did you choose it?</h4>
            <p>{$application->getQuestion3()}</p>
            <hr/>
            <h4>What do you do in your spare time?</h4>
            <p>{$application->getQuestion4()}</p>
            <hr/>
            <h4>Talk about a current event in technology and why it interests you.</h4>
            <p>{$application->getQuestion5()}</p>
            <hr/>
            <h4>Impress us</h4>
            <p>{$application->getQuestion6()}</p>
            <hr/>
            <h4>If you were to work on a personal project this semester that you could put on your resume, what would it be? (ex: an iOS app that is Tinder for dogs)</h4>
            <p>{$application->getQuestion7()}</p>
          </div>
        </div>
        {$admin_controls}
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Review</h1>
          </div>
          <div class="panel-body">
            <form method="post" action={'/review/' . $user->getID()}>
              <div class="form-group">
                <label for="review" class="control-label">Comments</label>
                <textarea class="form-control" rows={3} id="review" name="review">
                  {(is_null($review)) ? '' : $review->getComments()}
                </textarea>
              </div>
              <div class="form-group">
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="1" checked={(is_null($review)) ? false : $review->getRating() == 1} /> Strong No
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="2" checked={(is_null($review)) ? false : $review->getRating() == 2} /> Weak No
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="3" checked={(is_null($review)) ? false : $review->getRating() == 3} /> Neutral
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="4" checked={(is_null($review)) ? false : $review->getRating() == 4} /> Weak Yes
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="5" checked={(is_null($review)) ? false : $review->getRating() == 5} /> Strong Yes
                  </label>
                </div>
              </div>
              <button type="submit" name="id" value={(string) $application->getID()} class="btn btn-default">Submit</button>
            </form>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Average Rating</h1>
          </div>
          <div class="panel-body">
            <h1 class="text-center">{$avg_rating . ' / 5.00'}</h1>
          </div>
        </div>
        {self::getReviews($application)}
      </div>;
  }

  private static function getReviews(Application $application): ?:xhp {

    # Loop through the reviews
    $reviewList = Review::loadByApp($application->getID());
    $reviews = <ul class="list-group" />;
    foreach($reviewList as $row_app) {
      $user = User::load((int) $row_app->getUserID());
      $reviews->appendChild(
        <li class="list-group-item">
          <h4>{$user->getFullName()}</h4>
          <p>{$row_app->getComments()}</p>
        </li>
      );
    }

    # Loop through member feedback
    $feedbackList = Feedback::loadByUser($application->getUserID());
    $feedback = <ul class="list-group" />;
    foreach($feedbackList as $row_feedback) {
      # Skip empty feedback
      if($row_feedback->getComments() === '') {
        continue;
      }
      $user = User::load((int) $row_feedback->getReviewerID());
      $feedback->appendChild(
        <li class="list-group-item">
          <h4>{$user->getFullName()}</h4>
          <p>{$row_feedback->getComments()}</p>
        </li>
      );
    }

    $attendances = Attendance::loadForUser($application->getUserID());
    $events = <ul class="list-group" />;
    foreach($attendances as $attendance) {
      $event = Event::load($attendance->getEventID());
      $attendance = Attendance::loadForUserEvent($application->getUserID(), $event->getID());
      $events->appendChild(
        <li class="list-group-item">
          <h4>{$event->getName() . ' - ' . (($attendance->getStatus() == AttendanceState::Present) ? 'Present' : 'Not Present')}</h4>
        </li>
      );
    }

    return
      <div class="panel panel-default" role="tabpanel">
        <div class="panel-heading">
          <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="active">
              <a href="#reviews" aria-controls="home" role="tab" data-toggle="tab">Reviews</a>
            </li>
            <li role="presentation">
              <a href="#feedback" aria-controls="profile" role="tab" data-toggle="tab">Member Feedback</a>
            </li>
            <li role="presentation">
              <a href="#events" aria-controls="profile" role="tab" data-toggle="tab">Events Attended</a>
            </li>
          </ul>
        </div>
        <div class="tab-content">
          <br/>
          <div role="tabpanel" class="tab-pane active" id="reviews">{$reviews}</div>
          <div role="tabpanel" class="tab-pane" id="feedback">{$feedback}</div>
          <div role="tabpanel" class="tab-pane" id="events">{$events}</div>
        </div>
      </div>;
  }
}
