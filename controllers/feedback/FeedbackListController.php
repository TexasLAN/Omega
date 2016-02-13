<?hh //decl

class FeedbackListController extends BaseController {
  public static function getPath(): string {
    return '/feedback';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Active
        });
    $newConfig->setTitle('Feedback');
    return $newConfig;
  }

  public static function get(): :xhp {
    $table = <table class="table table-bordered table-striped sortable" />;
    $table->appendChild(
      <thead>
        <tr>
          <th>Name</th>
          <th data-defaultsort="disabled">Review</th>
          <th>Reviewed</th>
        </tr>
      </thead>
    );

    // Loop through all the applications that are submitted
    $userList = User::loadStates(Vector {
              UserState::Applicant,
              UserState::Candidate
            });
    $table_body = <tbody class="list" />;
    foreach($userList as $cur_user) {
      // Get the user the application belongs to
      $user = User::load((int)$cur_user->getID());

      // Get the current user's review
      $feedback = Feedback::loadByUserAndReviewer($cur_user->getID(), Session::getUser()->getID());

      // Append the applicant to the table as a new row
      $table_body->appendChild(
        <tr id={(string) $cur_user->getID()}>
          <td class="name">{$user->getFullName()}</td>
          <td><a href={'/feedback/' . $cur_user->getID()} class="btn btn-primary">Review</a></td>
          <td>{!is_null($feedback) ? "✔" : ""}</td>
        </tr>
      );
    }

    $table->appendChild($table_body);

    return
      <x:frag>
        <div id="feedback" class="well">
          <input class="search form-control" placeholder="Search" />
          <br/>
          {$table}
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
        <script src="/js/feedback.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </x:frag>;
  }
}
