<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<6fdad7bdabd7f26fd124d230d6e12229>>
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
      '/review' => 'ReviewListController',
      '/review/(?<id>\\d+)' => 'ReviewSingleController',
      '/settings' => 'SettingsController',
      '/signup' => 'SignupController',
    };
  }
}
