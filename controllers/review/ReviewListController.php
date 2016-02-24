<?hh //decl

class ReviewListController extends BaseController {
  public static function getPath(): string {
    return '/review';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setTitle('Review');
    return $newConfig;
  }

  private static function validateActions($user): bool {
    return $user->validateRole(UserRoleEnum::Reviewer) || $user->validateRole(UserRoleEnum::Admin);
  }

  public static function get(): :xhp {
    $user = Session::getUser();
    $table = <table class="table table-bordered table-striped sortable" />;
    $tableHeader = 
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Feedback</th>
    </tr>;
    if(self::validateActions($user)) {
      $tableHeader->appendChild(<th>{'# Reviews'}</th>);
      $tableHeader->appendChild(<th>Avg Rating</th>);
      $tableHeader->appendChild(<th data-defaultsort="disabled">Review</th>);
      $tableHeader->appendChild(<th>Reviewed</th>);
    }
    $table->appendChild(
      <thead>
        {$tableHeader}
      </thead>
    );

    // Loop through all the applications that are submitted
    $applicationList = Application::loadState(ApplicationState::Submitted);
    $table_body = <tbody class="list" />;
    foreach($applicationList as $row_app) {
      // Get the user the application belongs to
      $appUser = User::load($row_app->getUserID());

      // Skip the user if they're no longer an applicant or candidate
      if(!$appUser->isReviewable()) {
        continue;
      }

      $count = Review::getAppCount($row_app->getID());
      $avg_rating = Review::getAvgRating($row_app->getID());

      // Get the current user's review
      $cur_app = Review::loadByUserAndApp(Session::getUser()->getID(), $row_app->getID());

      $tableBodyCells = 
      <tr id={(string) $row_app->getID()}>
        <td>{(string) $row_app->getID()}</td>
        <td class="name">{$appUser->getFullName()}</td>
        <td class="email">{$appUser->getEmail()}</td>
        <td class="text-center"><a href={FeedbackSingleController::getPrePath() . $row_app->getUserID()} class="btn btn-primary">Feedback</a></td>
      </tr>;

      if(self::validateActions($user)) {
        $tableBodyCells->appendChild(<td class="text-center">{$count}</td>);
        $tableBodyCells->appendChild(<td class="text-center">{$avg_rating}</td>);
        $tableBodyCells->appendChild(<td class="text-center"><a href={'/review/' . $row_app->getID()} class="btn btn-primary">Review</a></td>);
        $tableBodyCells->appendChild(<td>{!is_null($cur_app) ? "✔" : ""}</td>);
      }
      // Append the applicant to the table as a new row
      $table_body->appendChild($tableBodyCells);
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

  public static function post(): void {
    $user = Session::getUser();
    if(!self::validateActions($user)) {
      Flash::set('error', 'You do not have the permissions to alter the applications!');
      Route::redirect(self::getPath());
      return;
    }

    if(isset($_POST['delete'])) {
      $user = User::load((int)$_POST['delete']);
      $app = Application::loadByUser($user->getID());
      UserMutator::delete($user->getID());
      ApplicationMutator::delete($app->getID());
      Flash::set('success', 'Application deleted successfully');
    } elseif(isset($_POST['candidate'])) {
      UserMutator::update((int)$_POST['candidate'])
        ->setMemberStatus(UserState::Candidate)
        ->save();
      Flash::set('success', 'Application promoted successfully');
    } else {
      // Upsert the review
      ReviewMutator::upsert(
        $_POST['review'],
        (int)$_POST['weight'],
        Session::getUser(),
        Application::load((int)$_POST['id'])
      );
    }
    Route::redirect(self::getPath());
  }
}
