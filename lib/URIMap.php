<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<f4b8a849d3f04554cb6ff720963ed37e>>
 */

final class URIMap {

  public function getURIMap(): Map<string, string> {
    return Map {
      '/' => 'FrontpageController',
      '/apply' => 'ApplyController',
      '/dashboard' => 'DashboardController',
      '/events/(?<id>\\d+)' => 'EventAttendanceController',
      '/events/admin' => 'EventsAdminController',
      '/feedback' => 'FeedbackListController',
      '/feedback/(?<id>\\d+)' => 'FeedbackSingleController',
      '/login' => 'LoginController',
      '/members' => 'MembersController',
      '/notify' => 'NotifyController',
      '/review/(?<id>\\d+)' => 'ReviewSingleController',
      '/settings' => 'SettingsController',
      '/signup' => 'SignupController',
    };
  }
}
