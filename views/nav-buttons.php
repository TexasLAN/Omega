<?hh // strict

final class :omega:nav-buttons extends :x:element {
  attribute User user, string controller;

  final protected function render(): :ul {
    $user = $this->getAttribute('user');
    $controller = $this->getAttribute('controller');

    $nav_buttons = <ul class="nav navbar-nav"></ul>;

    // Application Screen
    if ($user->getState() == UserState::Applicant) {
      $nav_buttons->appendChild(
        <li class={$controller === 'ApplyController' ? 'active' : ''}>
          <a href={ApplyController::getPath()}>Apply</a>
        </li>
      );
    }

    // Member List
    if ($user->getState() == UserState::Active || $user->getState() == UserState::Alum) {
      $nav_buttons->appendChild(
        <li
          class=
            {$controller === 'MembersController' || $controller === 'MemberProfileController' || $controller === 'MemberSettingsController'
              ? 'active'
              : ''}>
          <a href={MembersController::getPath()}>Members</a>
        </li>
      );
    }

    // Review List
    if ($user->getState() == UserState::Active) {
      $nav_buttons->appendChild(
        <li
          class=
            {($controller === 'ReviewListController' ||
              $controller === 'ReviewSingleController' ||
              $controller === 'FeedbackSingleController') ? 'active' : ''}>
          <a href={ReviewListController::getPath()}>Review</a>
        </li>
      );
    }

    // Events
    if ($user->getState() == UserState::Pledge ||
        $user->getState() == UserState::Active) {
      $nav_buttons->appendChild(
        <li
          class=
            {($controller === 'EventsListController' ||
              $controller === 'EventDetailsController') ? 'active' : ''}>
          <a href={EventsListController::getPath()}>Events</a>
        </li>
      );
    }

    //Standards Board Comments
    if(in_array(UserRoleEnum::Webmaster, $user->getRoles())) {
      $nav_buttons->appendChild(
        <li
          class=
            {($controller === 'StandardsBoardBoxController') ? 'active' : ''}>

          <a href={StandardsBoardBoxController::getPath()}>Events</a>
        </li>
        );
    }

    // Comments
    if ($user->getState() == UserState::Active) {
      $nav_buttons->appendChild(
        <li
          class=
            {($controller === 'CommentBoxController') ? 'active' : ''}>
          <a href={CommentBoxController::getPath()}>Comment Box</a>
        </li>
      );
    }

    // Notify Log
    if ($user->getState() == UserState::Active) {
      $nav_buttons->appendChild(
        <li
          class=
            {($controller === 'NotifyLogController' ||
              $controller === 'NotifyLogController') ? 'active' : ''}>
          <a href={NotifyLogController::getPath()}>Notification Logs</a>
        </li>
      );
    }

    // Notify Creation Screen
    if ($user->validateRole(UserRoleEnum::Admin) ||
        $user->validateRole(UserRoleEnum::Officer)) {
      $nav_buttons->appendChild(
        <li class={$controller === 'NotifyController' ? 'active' : ''}>
          <a href={NotifyController::getPath()}>Send Notification</a>
        </li>
      );
    }

    // Voting Apply
    if (Settings::getVotingStatus() == VotingStatus::Apply &&
        $user->getState() == UserState::Active) {
      $nav_buttons->appendChild(
        <li class={$controller === 'VoteApplyController' ? 'active' : ''}>
          <a href={VoteApplyController::getPath()}>Election Apply</a>
        </li>
      );
    }

    // Voting
    if (($user->validateRole(UserRoleEnum::Admin)) ||
        ((Settings::getVotingStatus() == VotingStatus::Apply ||
          Settings::getVotingStatus() == VotingStatus::Voting ||
          Settings::getVotingStatus() == VotingStatus::Results) &&
         ($user->getState() == UserState::Active))) {
      $nav_buttons->appendChild(
        <li class={$controller === 'VoteController' ? 'active' : ''}>
          <a href={VoteController::getPath()}>Vote</a>
        </li>
      );
    }

    // Admin only Settings
    if ($user->validateRole(UserRoleEnum::Admin)) {
      $nav_buttons->appendChild(
        <li class={$controller === 'SettingsController' ? 'active' : ''}>
          <a href={SettingsController::getPath()}>Site Settings</a>
        </li>
      );
    }

    return $nav_buttons;
  }
}
