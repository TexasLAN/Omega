<?hh // strict

final class :omega:nav-buttons extends :x:element {
  attribute User user, string controller;

  final protected function render(): :ul {
    $user = $this->getAttribute('user');
    $controller = $this->getAttribute('controller');

    $nav_buttons =
      <ul class="nav navbar-nav">
        <li class={$controller === 'MemberProfileController' ? 'active' : ''}>
          <a href={MemberProfileController::getPrePath() . $user->getID()}>Profile</a>
        </li>
      </ul>;

    // Application Screen
    if($user->getState() == UserState::Applicant) {
      $nav_buttons->appendChild(
        <li class={$controller === 'ApplyController' ? 'active' : ''}>
          <a href={ApplyController::getPath()}>Apply</a>
        </li>
      );
    }

    // Member List
    if($user->getState() == UserState::Member) {
      $nav_buttons->appendChild(
        <li class={$controller === 'MembersController' ? 'active' : ''}>
          <a href={MembersController::getPath()}>Members</a>
        </li>
      );
    }

    // Feedback List
    if($user->getState() == UserState::Member) {
      $nav_buttons->appendChild(
        <li class={($controller === 'FeedbackListController' || $controller === 'FeedbackSingleController') ? 'active' : ''}>
          <a href={FeedbackListController::getPath()}>Applicant Feedback</a>
        </li>
      );
    }

    // Review List
    if($user->validateRole(UserRoleEnum::Admin) || $user->validateRole(UserRoleEnum::Reviewer)) {
      $nav_buttons->appendChild(
        <li class={($controller === 'ReviewListController' || $controller === 'ReviewSingleController') ? 'active' : ''}>
          <a href={ReviewListController::getPath()}>Review</a>
        </li>
      );
    }

    // Events
    if($user->getState() == UserState::Pledge || $user->getState() == UserState::Member) {
      $nav_buttons->appendChild(
        <li class={($controller === 'EventsListController' || $controller === 'EventDetailsController') ? 'active' : ''}>
          <a href={EventsListController::getPath()}>Events</a>
        </li>
      );
    }

    // Notify Log
    if($user->getState() == UserState::Pledge || $user->getState() == UserState::Member) {
      $nav_buttons->appendChild(
        <li class={($controller === 'NotifyLogController' || $controller === 'NotifyLogController') ? 'active' : ''}>
          <a href={NotifyLogController::getPath()}>Notification Logs</a>
        </li>
      );
    }

    // Notify Creation Screen
    if($user->validateRole(UserRoleEnum::Admin) || $user->validateRole(UserRoleEnum::Officer)) {
      $nav_buttons->appendChild(
        <li class={$controller === 'NotifyController' ? 'active' : ''}>
          <a href={NotifyController::getPath()}>Send Notification</a>
        </li>
      );
    }

    // Admin only Settings
    if($user->validateRole(UserRoleEnum::Admin)) {
      $nav_buttons->appendChild(
        <li class={$controller === 'SettingsController' ? 'active' : ''}>
          <a href={SettingsController::getPath()}>Site Settings</a>
        </li>
      );
    }

    return $nav_buttons;
  }
}
