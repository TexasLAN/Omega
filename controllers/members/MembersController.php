<?hh

class MembersController extends BaseController {
  public static function getPath(): string {
    return '/members';
  }

  public static function getConfig(): ControllerConfig {
    $newConfig = new ControllerConfig();
    $newConfig->setUserState(Vector {UserState::Active});
    $newConfig->setTitle('Review');
    return $newConfig;
  }

  private static function validateActions($user): bool {
    return $user->validateRole(UserRoleEnum::Admin);
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    $tabList = <ul class="nav nav-tabs nav-justified" role="tablist" />;
    $tabPanel = <div class="tab-content" />;
    foreach (UserState::getValues() as $name => $value) {
      // Tab List
      $href = "#".strtolower($name);
      if ($value == UserState::Active) {
        $listItem = <li role="presentation" class="active" />;
        $listItem->appendChild(
          <a href={$href} aria-controls="home" role="tab" data-toggle="tab">
            {$name}
          </a>
        );
      } else {
        $listItem = <li role="presentation" />;
        $listItem->appendChild(
          <a
            href={$href}
            aria-controls="profile"
            role="tab"
            data-toggle="tab">
            {$name}
          </a>
        );
      }
      // If it is disabled only let admins see the tab
      if ($value != UserState::Disabled ||
          $user->validateRole(UserRoleEnum::Admin)) {
        $tabList->appendChild($listItem);
      }

      // Tab Content
      $id = strtolower($name);
      $contentItem =
        <div role="tabpanel" class="btn-toolbar tab-pane" id={$id} />;
      if ($value == UserState::Active) {
        $contentItem =
          <div
            role="tabpanel"
            class="btn-toolbar tab-pane active"
            id={$id}
          />;
      }

      if (self::validateActions($user)) {
        if ($value == UserState::Pledge ||
            $value == UserState::Candidate ||
            $value == UserState::Applicant) {
          $contentItem->appendChild(
            <button
              type="button"
              class="btn btn-danger"
              style="margin-bottom: 10px;"
              data-toggle="modal"
              data-target="#disableConfirm"
              data-type="state"
              data-id={(string) $value}>
              Disable
            </button>
          );
        }
      }

      $memberContent = self::getMembersByState($value);
      $contentItem->appendChild($memberContent);
      // If it is disabled only let admins see the tab
      if ($value != UserState::Disabled ||
          $user->validateRole(UserRoleEnum::Admin)) {
        $tabPanel->appendChild($contentItem);
      }
    }

    return
      <div class="well" role="tabpanel">
        {$tabList}
        <br />
        {$tabPanel}
        {self::getRoleEditModal()}
        {self::getDeleteModal()}
        {self::getDisableModal()}
        <script src="/js/clipboard.min.js"></script>
        <script src="/js/members.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getMembersByState(UserState $state): :table {
    $user = Session::getUser();
    $members = <tbody />;

    // Loop through all users with the specified status
    $userList = User::loadStates(Vector {$state});
    foreach ($userList as $row_user) {
      $roles = UserRole::getRoles((int) $row_user->getID());
      // Generate the action buttons based off the user's role and status
      $buttons = <form class="btn-toolbar" method="post" action="/members" />;
      $buttons->appendChild(
        <input
          type="hidden"
          name="id"
          value={(string) $row_user->getID()}
        />
      );

      if (self::validateActions($user)) {
        if ($row_user->getState() == UserState::Disabled) {
          $buttons->appendChild(
            <button
              name="state_change"
              class="btn btn-primary"
              value={(string) UserState::Applicant}
              type="submit">
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
              data-id={$row_user->getID()}>
              Delete
            </button>
          );
        } else if ($row_user->getState() == UserState::Applicant) {
          $buttons->appendChild(
            <button
              name="state_change"
              class="btn btn-primary"
              value={(string) UserState::Candidate}
              type="submit">
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
              data-id={$row_user->getID()}>
              Disable
            </button>
          );
        } else if ($row_user->getState() == UserState::Candidate) {
          $buttons->appendChild(
            <button
              name="state_change"
              class="btn btn-primary"
              value={(string) UserState::Pledge}
              type="submit">
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
              data-id={$row_user->getID()}>
              Disable
            </button>
          );
        } else if ($row_user->getState() == UserState::Pledge) {
          $buttons->appendChild(
            <button
              name="state_change"
              class="btn btn-primary"
              value={(string) UserState::Active}
              type="submit">
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
              data-id={$row_user->getID()}>
              Disable
            </button>
          );
        } else if ($row_user->getState() == UserState::Inactive) {
          $buttons->appendChild(
            <button
              name="state_change"
              class="btn btn-primary"
              value={(string) UserState::Alum}
              type="submit">
              Promote to Alum
            </button>
          );
          $buttons->appendChild(
            <button
              name="state_change"
              class="btn btn-primary"
              value={(string) UserState::Active}
              type="submit">
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
              data-id={$row_user->getID()}>
              Disable
            </button>
          );
        } else if ($row_user->getState() == UserState::Active) {
          $buttons->appendChild(
            <button
              name="state_change"
              class="btn btn-primary"
              value={(string) UserState::Alum}
              type="submit">
              Promote to Alum
            </button>
          );
          $buttons->appendChild(
            <button
              name="state_change"
              class="btn btn-primary"
              value={(string) UserState::Inactive}
              type="submit">
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
              data-id={$row_user->getID()}>
              Disable
            </button>
          );
          $buttons->appendChild(
            <button
              type="button"
              class="btn btn-primary"
              data-toggle="modal"
              data-target="#editRoles"
              data-id={$row_user->getID()}
              data-name={$row_user->getFullName()}
              data-roles={json_encode($roles)}>
              Edit Roles
            </button>
          );
        }
      }

      // Calculate event attendance
      $eventPresent = Attendance::countUserAttendance(
        $row_user->getID(),
        AttendanceState::Present,
        NULL,
      );
      $eventNotPresent = Attendance::countUserAttendance(
        $row_user->getID(),
        AttendanceState::NotPresent,
        NULL,
      );
      $eventPercent =
        ($eventPresent / ($eventPresent + $eventNotPresent)) * 100;
      $eventAttendText =
        ($eventPresent + $eventNotPresent == 0) ? 'N/A' : $eventPercent.'%';

      // Calculate gm attendance
      $gmPresent = Attendance::countUserAttendance(
        $row_user->getID(),
        AttendanceState::Present,
        EventType::GeneralMeeting,
      );
      $gmNotPresent = Attendance::countUserAttendance(
        $row_user->getID(),
        AttendanceState::NotPresent,
        EventType::GeneralMeeting,
      );
      $gmPercent = ($gmPresent / ($gmPresent + $gmNotPresent)) * 100;
      $gmAttendText =
        ($gmPresent + $gmNotPresent == 0) ? 'N/A' : $gmPercent.'%';

      // Append the row to the table
      $members->appendChild(
        <tr>
          <td>
            <a
              href=
                {MemberProfileController::getPrePath().$row_user->getID()}>
              {$row_user->getFullName()}
            </a>
          </td>
          <td>{$row_user->getEmail()}</td>
          <td>{number_format($eventAttendText, 2, '.', '')}</td>
          <td>{number_format($gmAttendText, 2, '.', '')}</td>
          <td>{$row_user->getPointsForAttendState(AttendanceState::Present) + 
            $row_user->getPointsForAttendState(AttendanceState::Excused)}</td>
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
            <th>Event %</th>
            <th>GM %</th>
            <th>Total Points</th>
            <th data-defaultsort="disabled">Actions</th>
          </tr>
        </thead>
        {$members}
      </table>;

  }

  private static function getRoleEditModal(): :xhp {
    $form = <form action="/members" method="post" />;
    foreach (UserRoleEnum::getValues() as $name => $value) {
      $form->appendChild(
        <div class="checkbox">
          <label>
            <input type="checkbox" id={$value} name={$value} />
            {ucwords($value)}
          </label>
        </div>
      );
    }
    $form->appendChild(<input type="hidden" name="id" />);
    $form->appendChild(<input type="hidden" name="role_save" />);
    return
      <div
        class="modal fade"
        id="editRoles"
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
              <h3 class="modal-title" id="editRolesName" />
            </div>
            <div class="modal-body">
              {$form}
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

  private static function getDeleteModal(): :xhp {
    return
      <div
        class="modal fade"
        id="deleteConfirm"
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
              <h3 class="modal-title">Delete Confirmation</h3>
            </div>
            <div class="modal-body">Are you sure you want to delete?</div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-default"
                data-dismiss="modal">
                Cancel
              </button>
              <button
                name="delete"
                type="button"
                class="btn btn-danger"
                id="delete-submit">
                Delete
              </button>
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
      <div
        class="modal fade"
        id="disableConfirm"
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
              <h3 class="modal-title">Disable Confirmation</h3>
            </div>
            <div class="modal-body">Are you sure you want to disable?</div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-default"
                data-dismiss="modal">
                Cancel
              </button>
              <button
                name="disable"
                type="button"
                class="btn btn-danger"
                id="disable-submit">
                Disable
              </button>
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

  public static function post(): void {
    if (isset($_POST['delete'])) {
      // Deletes a user by by single or by group
      if (isset($_POST['delete_type']) && $_POST['delete_type'] == 'single') {
        UserMutator::delete((int) $_POST['delete_id']);
      } else if (isset($_POST['delete_type']) &&
                 $_POST['delete_type'] == 'state') {
        $state = UserState::assert($_POST['delete_id']);
        UserMutator::deleteByState($state);
      }
    } else if (isset($_POST['disable'])) {
      // Disables a user by by single or by group
      if (isset($_POST['disable_type']) &&
          $_POST['disable_type'] == 'single') {
        UserMutator::update((int) $_POST['disable_id'])
          ->setMemberStatus(UserState::Disabled)
          ->save();
      } else if (isset($_POST['disable_type']) &&
                 $_POST['disable_type'] == 'state') {
        $state = UserState::assert($_POST['disable_id']);
        UserMutator::disableByState($state);
      }
    } else if (isset($_POST['state_change'])) {
      // Makes a member a candidate
      if (UserState::Pledge == (int) $_POST['state_change']) {
        UserMutator::update((int) $_POST['id'])
          ->setClass(Settings::getCurrentClass())
          ->save();
      }
      UserMutator::update((int) $_POST['id'])
        ->setMemberStatus((int) $_POST['state_change'])
        ->save();
    } else if (isset($_POST['role_save'])) {
      // Saves the editted roled
      foreach (UserRoleEnum::getValues() as $name => $role) {
        $user_roles = UserRole::getRoles((int) $_POST['id']);

        if (isset($_POST[$role])) {
          if (!in_array($role, $user_roles)) {
            UserRole::insert($role, (int) $_POST['id']);
          }
        } else {
          if (in_array($role, $user_roles)) {
            UserRole::delete($role, (int) $_POST['id']);
          }
        }
      }
    }

    Route::redirect(MembersController::getPath());
  }
}
