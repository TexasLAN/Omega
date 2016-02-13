<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<f021379c28cd304a1051eeac0d4e0ff2>>
 */

final class URIMap {

  public function getURIMap(): Map<string, string> {
    return Map {
      '/' => 'FrontpageController',
      '/apply' => 'ApplyController',
      '/event' => 'EventsListController',
      '/event/(?<id>\\d+)' => 'EventDetailsController',
      '/feedback' => 'FeedbackListController',
      '/feedback/(?<id>\\d+)' => 'FeedbackSingleController',
      '/login' => 'LoginController',
      '/members' => 'MembersController',
      '/members/(?<id>\\d+)' => 'MemberProfileController',
      '/members/settings' => 'MemberSettingsController',
      '/notify' => 'NotifyController',
      '/notify/log' => 'NotifyLogController',
      '/password/(?<forgot_token>\\w+)' => 'ForgotPasswordController',
      '/review' => 'ReviewListController',
      '/review/(?<id>\\d+)' => 'ReviewSingleController',
      '/settings' => 'SettingsController',
      '/signup' => 'SignupController',
      '/venmo' => 'VenmoController',
      '/vote' => 'VoteController',
      '/voteapply' => 'VoteApplyController',
      '/voteapply/(?<id>\\d+)' => 'VoteApplicationController',
      '/voteapply/(?<id>\\d+)/(?<user_id>\\d+)' =>
        'VoteApplicationProfileController',
      '/votesetup' => 'VoteSetupController',
    };
  }
}
