<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<24632ec2c6895c660a879c45468f3526>>
 */

final class URIMap {

  public function getURIMap(): Map<string, string> {
    return Map {
      '/' => 'FrontpageController',
      '/api/oauth/authorize' => 'OAuthAuthorizeController',
      '/api/oauth/token' => 'OAuthTokenController',
      '/api/users/me' => 'UserApiController',
      '/apply' => 'ApplyController',
      '/standards_comment' => 'StandardsBoardBoxController',
      '/comment' => 'CommentBoxController',
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
