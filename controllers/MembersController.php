<?hh

class MembersController extends BaseController {
  public static function getPath(): string {
    return '/members';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(
      Vector {
        UserState::Member
        });
    $newConfig->setUserRoles(
      Vector {
        UserRoleEnum::Admin
        });
    $newConfig->setTitle('Review');
    return $newConfig;
  }

  public static function get(): :xhp {
    return
      <div class="well" role="tabpanel">
        <ul class="nav nav-tabs nav-justified" role="tablist">
          <li role="presentation">
            <a href="#alum" aria-controls="profile" role="tab" data-toggle="tab">Alums</a>
          </li>
          <li role="presentation" class="active">
            <a href="#members" aria-controls="home" role="tab" data-toggle="tab">Members</a>
          </li>
          <li role="presentation">
            <a href="#inactive" aria-controls="profile" role="tab" data-toggle="tab">Inactives</a>
          </li>
          <li role="presentation">
            <a href="#pledges" aria-controls="profile" role="tab" data-toggle="tab">Pledges</a>
          </li>
          <li role="presentation">
            <a href="#candidates" aria-controls="profile" role="tab" data-toggle="tab">Candidates</a>
          </li>
          <li role="presentation">
            <a href="#applicants" aria-controls="profile" role="tab" data-toggle="tab">Applicants</a>
          </li>
        </ul>
        <br />
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane" id="alum">
            <button class="btn btn-primary btn-clipboard" data-clipboard-text={self::getEmailList(UserState::Alum)}>
              Email List
            </button>
            {self::getMembersByStatus(UserState::Alum)}
          </div>
          <div role="tabpanel" class="tab-pane active" id="members">
            <button class="btn btn-primary btn-clipboard" data-clipboard-text={self::getEmailList(UserState::Member)}>
              Email List
            </button>
            {self::getMembersByStatus(UserState::Member)}
          </div>
          <div role="tabpanel" class="tab-pane" id="inactive">
            <button class="btn btn-primary btn-clipboard" data-clipboard-text={self::getEmailList(UserState::Inactive)}>
              Email List
            </button>
            {self::getMembersByStatus(UserState::Inactive)}
          </div>
          <div role="tabpanel" class="tab-pane" id="pledges">
            <button class="btn btn-primary btn-clipboard" data-clipboard-text={self::getEmailList(UserState::Pledge)}>
              Email List
            </button>
            <button
              type="button"
              class="btn btn-danger"
              data-toggle="modal"
              data-target="#deleteConfirm"
              data-type="state"
              data-id={(string) UserState::Pledge}>
              Delete
            </button>
            {self::getMembersByStatus(UserState::Pledge)}
          </div>
          <div role="tabpanel" class="tab-pane" id="candidates">
            <button class="btn btn-primary btn-clipboard" data-clipboard-text={self::getEmailList(UserState::Candidate)}>
              Email List
            </button>
            <button
              type="button"
              class="btn btn-danger"
              data-toggle="modal"
              data-target="#deleteConfirm"
              data-type="state"
              data-id={(string) UserState::Candidate}>
              Delete
            </button>
            {self::getMembersByStatus(UserState::Candidate)}
          </div>
          <div role="tabpanel" class="tab-pane" id="applicants">
            <button class="btn btn-primary btn-clipboard" data-clipboard-text={self::getEmailList(UserState::Applicant)}>
              Email List
            </button>
            <button
              type="button"
              class="btn btn-danger"
              data-toggle="modal"
              data-target="#deleteConfirm"
              data-type="state"
              data-id={(string) UserState::Applicant}>
              Delete
            </button>
            {self::getMembersByStatus(UserState::Applicant)}
          </div>
        </div>
        {self::getModal()}
        {self::getDeleteModal()}
        <script src="/js/clipboard.min.js"></script>
        <script src="/js/members.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getMembersByStatus(int $status): :table {
    $members = <tbody />;

    # Loop through all users with the specified status
    $query = DB::query("SELECT * FROM users WHERE member_status=%s", $status);
    foreach($query as $row) {
      $roles = UserRole::getRoles((int)$row['id']);
      # Generate the action buttons based off the user's role and status
      $buttons = <form class="btn-toolbar" method="post" action="/members" />;
      if($row['member_status'] == UserState::Applicant) {
        $buttons->appendChild(
          <button name="candidate" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to candidate
          </button>
        );
      } elseif ($row['member_status'] == UserState::Candidate) {
        $buttons->appendChild(
          <button name="pledge" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to pledge
          </button>
        );
      } elseif ($row['member_status'] == UserState::Pledge) {
        $buttons->appendChild(
          <button name="active" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to Active
          </button>
        );
      } elseif ($row['member_status'] == UserState::Inactive) {
        $buttons->appendChild(
          <button name="alum" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to Alum
          </button>
        );
        $buttons->appendChild(
          <button name="active" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to Active
          </button>
        );
      } elseif ($row['member_status'] == UserState::Member){
        $buttons->appendChild(
          <button name="alum" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to Alum
          </button>
        );
        $buttons->appendChild(
          <button name="inactive" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to Inactive
          </button>
        );
        $buttons->appendChild(
          <button
            type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#editRoles"
            data-id={$row['id']}
            data-name={$row['fname'] . ' ' . $row['lname']}
            data-roles={json_encode($roles)}>
            Edit Roles
          </button>
        );
      }
      $buttons->appendChild(
          <button
            type="button"
            class="btn btn-danger"
            data-toggle="modal"
            data-target="#deleteConfirm"
            data-type="single"
            data-id={$row['id']}>
            Delete
          </button>
        );

      # Append the row to the table
      $members->appendChild(
        <tr>
          <td>{$row['fname'] . ' ' . $row['lname']}</td>
          <td>{$row['email']}</td>
          <td>{$buttons}</td>
        </tr>
      );
    }

    return
      <table class="table table-bordered table-striped sortable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th data-defaultsort="disabled">Actions</th>
          </tr>
        </thead>
        {$members}
      </table>;

  }


  private static function getModal(): :xhp {
    $form = <form action="/members" method="post" />;
    foreach(UserRoleEnum::getValues() as $name => $value) {
      $form->appendChild(
        <div class="checkbox">
          <label>
            <input type="checkbox" id={$value} name={$value} /> {ucwords($value)}
          </label>
        </div>
      );
    }
    $form->appendChild(
      <input type="hidden" name="id" />
    );
    $form->appendChild(
      <input type="hidden" name="role_save" />
    );
    return
      <div class="modal fade" id="editRoles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="editRolesName" />
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

  private static function getDeleteModal(): :xhp {
    return
      <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title">Delete Confirmation</h3>
            </div>
            <div class="modal-body">
              Are you sure you want to delete?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button name="delete" type="button" class="btn btn-danger" id="delete-submit">Delete</button>
              <form action="/members" method="post">
                <input type="hidden" name="delete" />
                <input type="hidden" name="delete_id" id="delete_id" />
                <input type="hidden" name="delete_type" id="delete_type" />
              </form>
            </div>
          </div>
        </div>
      </div>;
  }
  private static function getEmailList(int $status): string {
    $query = DB::query("SELECT * FROM users WHERE member_status=%s", $status);

    $email_export_str = '';
    $delim = '';
    foreach($query as $row) {
      $email_export_str .= $delim . $row['fname'] . ' ' . $row['lname'] . ' <' . $row['email'] . '>';
      $delim = ', ';
    }
      
    return $email_export_str;
  }

  public static function post(): void {
    if(isset($_POST['delete'])) {
      // Deletes a user by by single or by group
      if(isset($_POST['delete_type']) && $_POST['delete_type'] == 'single') {
        UserMutator::delete((int)$_POST['delete_id']);
      } elseif(isset($_POST['delete_type']) && $_POST['delete_type'] == 'state') {
        DB::delete("users", "member_status=%s", $_POST['delete_id']);
      }
    } elseif (isset($_POST['candidate'])) {
      // Makes a member a candidate
      UserMutator::update((int)$_POST['candidate'])
        ->setMemberStatus(UserState::Candidate)
        ->save();
    } elseif (isset($_POST['pledge'])) {
      // Makes a member pledge
      UserMutator::update((int)$_POST['pledge'])
        ->setMemberStatus(UserState::Pledge)
        ->save();
    } elseif (isset($_POST['active'])) {
      // Makes a member active
      UserMutator::update((int)$_POST['active'])
        ->setMemberStatus(UserState::Member)
        ->save();
    } elseif (isset($_POST['alum'])) {
      // Makes a member alum
      UserMutator::update((int)$_POST['alum'])
        ->setMemberStatus(UserState::Alum)
        ->save();
    } elseif (isset($_POST['inactive'])) {
      // Makes a member inactive
      UserMutator::update((int)$_POST['inactive'])
        ->setMemberStatus(UserState::Inactive)
        ->save();
    } elseif (isset($_POST['role_save'])) {
      // Saves the editted roled
      foreach(UserRoleEnum::getValues() as $name => $role) {
        $user_roles = UserRole::getRoles((int)$_POST['id']);

        if(isset($_POST[$role])) {
          if(!in_array($role, $user_roles)) {
            UserRole::insert($role, (int)$_POST['id']);
          }
        } else {
          if(in_array($role, $user_roles)) {
            UserRole::delete($role, (int)$_POST['id']);
          }
        }
      }
    }

    Route::redirect(MembersController::getPath());
  }
}
