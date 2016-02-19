<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<ab02b27daf67857e5606a3b4030f4bd9>>
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
      '/votecandidate' => 'VoteCandidateController',
    };
  }
}
