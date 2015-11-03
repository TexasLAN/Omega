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
    $tabList = <ul class="nav nav-tabs nav-justified" role="tablist"/>;
    $tabPanel = <div class="tab-content"/>;
    foreach(UserState::getValues() as $name => $value) {
      // Tab List
      $href = "#" . strtolower($name);
      if($value == UserState::Member) {
        $listItem = <li role="presentation" class="active"/>;
        $listItem->appendChild(
          <a href={$href} aria-controls="home" role="tab" data-toggle="tab">{$name}</a>
        );
      } else {
        $listItem = <li role="presentation"/>;
        $listItem->appendChild(
          <a href={$href} aria-controls="profile" role="tab" data-toggle="tab">{$name}</a>
        );
      }
      $tabList->appendChild($listItem);

      // Tab Content
      $id = strtolower($name);
      $contentItem = <div role="tabpanel" class="tab-pane" id={$id}/>;
      if($value == UserState::Member) {
        $contentItem = <div role="tabpanel" class="tab-pane active" id={$id}/>;
      }
      $contentItem->appendChild(
        <button class="btn btn-primary btn-clipboard" data-clipboard-text={self::getEmailList($value)}>
          Email List
        </button>
      );
      if($value == UserState::Pledge || $value == UserState::Candidate || $value == UserState::Applicant) {
        $contentItem->appendChild(
          <button
            type="button"
            class="btn btn-danger"
            data-toggle="modal"
            data-target="#disableConfirm"
            data-type="state"
            data-id={(string) $value}>
            Disable
          </button>
        );
      } elseif($value == UserState::Disabled) {
        $contentItem->appendChild(
          <button
            type="button"
            class="btn btn-danger"
            data-toggle="modal"
            data-target="#deleteConfirm"
            data-type="state"
            data-id={(string) $value}>
            Delete
          </button>
        );
      }
      $memberContent = self::getMembersByStatus($value);
      $contentItem->appendChild($memberContent);
      $tabPanel->appendChild($contentItem);
    }

    return
      <div class="well" role="tabpanel">
        {$tabList}
        <br />
        {$tabPanel}
        {self::getModal()}
        {self::getDeleteModal()}
        {self::getDisableModal()}
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
      $buttons->appendChild(
      <input type="hidden" name="id" value={$row['id']} />
    );

      if($row['member_status'] == UserState::Disabled) {
        $buttons->appendChild(
          <button name="state_change" class="btn btn-primary" value={(string) UserState::Applicant} type="submit">
            Enable to Applicant
          </button>
        );
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
      } elseif($row['member_status'] == UserState::Applicant) {
        $buttons->appendChild(
          <button name="state_change" class="btn btn-primary" value={(string) UserState::Candidate} type="submit">
            Promote to candidate
          </button>
        );
        $buttons->appendChild(
          <button
            type="button"
            class="btn btn-danger"
            data-toggle="modal"
            data-target="#disableConfirm"
            data-type="single"
            data-id={$row['id']}>
            Disable
          </button>
        );
      } elseif ($row['member_status'] == UserState::Candidate) {
        $buttons->appendChild(
          <button name="state_change" class="btn btn-primary" value={(string) UserState::Pledge} type="submit">
            Promote to pledge
          </button>
        );
        $buttons->appendChild(
          <button
            type="button"
            class="btn btn-danger"
            data-toggle="modal"
            data-target="#disableConfirm"
            data-type="single"
            data-id={$row['id']}>
            Disable
          </button>
        );
      } elseif ($row['member_status'] == UserState::Pledge) {
        $buttons->appendChild(
          <button name="state_change" class="btn btn-primary" value={(string) UserState::Member} type="submit">
            Promote to Active
          </button>
        );
        $buttons->appendChild(
          <button
            type="button"
            class="btn btn-danger"
            data-toggle="modal"
            data-target="#disableConfirm"
            data-type="single"
            data-id={$row['id']}>
            Disable
          </button>
        );
      } elseif ($row['member_status'] == UserState::Inactive) {
        $buttons->appendChild(
          <button name="state_change" class="btn btn-primary" value={(string) UserState::Alum} type="submit">
            Promote to Alum
          </button>
        );
        $buttons->appendChild(
          <button name="state_change" class="btn btn-primary" value={(string) UserState::Member} type="submit">
            Promote to Active
          </button>
        );
        $buttons->appendChild(
          <button
            type="button"
            class="btn btn-danger"
            data-toggle="modal"
            data-target="#disableConfirm"
            data-type="single"
            data-id={$row['id']}>
            Disable
          </button>
        );
      } elseif ($row['member_status'] == UserState::Member){
        $buttons->appendChild(
          <button name="state_change" class="btn btn-primary" value={(string) UserState::Alum} type="submit">
            Promote to Alum
          </button>
        );
        $buttons->appendChild(
          <button name="state_change" class="btn btn-primary" value={(string) UserState::Inactive} type="submit">
            Promote to Inactive
          </button>
        );
        $buttons->appendChild(
          <button
            type="button"
            class="btn btn-danger"
            data-toggle="modal"
            data-target="#disableConfirm"
            data-type="single"
            data-id={$row['id']}>
            Disable
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

  private static function getDisableModal(): :xhp {
    return
      <div class="modal fade" id="disableConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title">Disable Confirmation</h3>
            </div>
            <div class="modal-body">
              Are you sure you want to disable?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button name="disable" type="button" class="btn btn-danger" id="disable-submit">Disable</button>
              <form action="/members" method="post">
                <input type="hidden" name="disable" />
                <input type="hidden" name="disable_id" id="disable_id" />
                <input type="hidden" name="disable_type" id="disable_type" />
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
    } elseif(isset($_POST['disable'])) {
      // Disables a user by by single or by group
      if(isset($_POST['disable_type']) && $_POST['disable_type'] == 'single') {
        UserMutator::update((int)$_POST['disable_id'])
        ->setMemberStatus(UserState::Disabled)
        ->save();
      } elseif(isset($_POST['disable_type']) && $_POST['disable_type'] == 'state') {
        $paramData = Map {
          'member_status' => UserState::Disabled
        };
        DB::update("users", $paramData->toArray(), "member_status=%s", $_POST['disable_id']);
      }
    }elseif (isset($_POST['state_change'])) {
      // Makes a member a candidate
      UserMutator::update((int)$_POST['id'])
        ->setMemberStatus((int)$_POST['state_change'])
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
