<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * @generated SignedSource<<0875e33a00d698d58356263fa16f364f>>
 */

final class URIMap {

  public function getURIMap(): Map<string, string> {
    return Map {
      '/' => 'FrontpageController',
      '/apply' => 'ApplyController',
      '/dashboard' => 'DashboardController',
      '/events/admin' => 'EventsAdminController',
      '/events/attendance/(?<id>\\d+)' => 'EventAttendanceController',
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
