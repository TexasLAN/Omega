<?hh //decl

class ReviewListController extends BaseController {
  public static function getPath(): string {
    return '/review';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Member
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
    $table = <table class="table table-bordered table-striped sortable" />;
    $table->appendChild(
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>{'# Reviews'}</th>
          <th>Avg Rating</th>
          <th data-defaultsort="disabled">Review</th>
          <th>Reviewed</th>
        </tr>
      </thead>
    );

    // Loop through all the applications that are submitted
    $applicationList = Application::loadState(ApplicationState::Submitted);
    $table_body = <tbody class="list" />;
    foreach($applicationList as $row_app) {
      // Get the user the application belongs to
      $user = User::load($row_app->getUserID());

      // Skip the user if they're no longer an applicant or candidate
      if(!$user->isReviewable()) {
        continue;
      }

      $count = Review::getAppCount($row_app->getID());
      $avg_rating = Review::getAvgRating($row_app->getID());

      // Get the current user's review
      $cur_app = Review::loadByUserAndApp(Session::getUser()->getID(), $row_app->getID());

      // Append the applicant to the table as a new row
      $table_body->appendChild(
        <tr id={(string) $row_app->getID()}>
          <td>{(string) $row_app->getID()}</td>
          <td class="name">{$user->getFirstName() . ' ' . $user->getLastName()}</td>
          <td class="email">{$user->getEmail()}</td>
          <td class="text-center">{$count}</td>
          <td class="text-center">{$avg_rating}</td>
          <td class="text-center"><a href={'/review/' . $row_app->getID()} class="btn btn-primary">Review</a></td>
          <td>{!is_null($cur_app) ? "✔" : ""}</td>
        </tr>
      );
    }

    $table->appendChild($table_body);

    return
      <x:frag>
        <div id="applications" class="well">
          <input class="search form-control" placeholder="Search" />
          <br/>
          {$table}
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
        <script src="/js/review.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </x:frag>;
  }
}
