<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<ff661b7c0e1ffe9444eb88c2ae91217d>>
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
      '/suggestion' => 'SuggestionBoxController',
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
