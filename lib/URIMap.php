<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<e3a925f97494024eaa4d48fbb7419e21>>
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
      '/notify/log' => 'NotifyLogController',
      '/review' => 'ReviewListController',
      '/review/(?<id>\\d+)' => 'ReviewSingleController',
      '/settings' => 'SettingsController',
      '/signup' => 'SignupController',
    };
  }
}
