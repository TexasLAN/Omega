<?hh //decl

class FeedbackListController extends BaseController {
  public static function getPath(): string {
    return '/feedback';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Member
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
    $query = DB::query("SELECT * FROM users WHERE member_status=%s OR member_status=%s", UserState::Applicant, UserState::Candidate);
    $table_body = <tbody class="list" />;
    foreach($query as $row) {
      // Get the user the application belongs to
      $user = User::load((int)$row['id']);

      // Get the current user's review
      $feedback = Feedback::gen($row['id'], Session::getUser()->getID());

      // Append the applicant to the table as a new row
      $table_body->appendChild(
        <tr id={$row['id']}>
          <td class="name">{$user->getFirstName() . ' ' . $user->getLastName()}</td>
          <td><a href={'/feedback/' . $row['id']} class="btn btn-primary">Review</a></td>
          <td>{$feedback->getComments() != '' ? "✔" : ""}</td>
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
