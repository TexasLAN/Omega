<?hh

enum VoteRoleEnum: int as int {

  President = 0;
  Administration = 1;
  Treasurer = 2;
  Social = 3;
  Service = 4;
  NewMember = 5;
  Standards = 6;
  Risk = 7;
  Webmaster = 8;
  Brotherhood = 9;
  Historian = 10;

}

class VoteRole {
  public static function getRoleName(int $value): string {
    switch ($value) {
      case VoteRoleEnum::President: return 'President';
      case VoteRoleEnum::Administration: return 'VP of Administration';
      case VoteRoleEnum::Treasurer: return 'Treasurer';
      case VoteRoleEnum::Social: return 'VP of Social Affairs';
      case VoteRoleEnum::Service: return 'VP of Service Activities';
      case VoteRoleEnum::NewMember: return 'VP of New Member Services';
      case VoteRoleEnum::Standards: return 'VP of Standards';
      case VoteRoleEnum::Risk: return 'Risk Management';
      case VoteRoleEnum::Webmaster: return 'Webmaster';
      case VoteRoleEnum::Brotherhood: return 'Brotherhood';
      case VoteRoleEnum::Historian: return 'Historian';
      default: return '';
    }
  }

  public static function isVotingPosition(int $value): bool {
    switch ($value) {
      case VoteRoleEnum::President:
      case VoteRoleEnum::Administration:
      case VoteRoleEnum::Treasurer:
      case VoteRoleEnum::Social:
      case VoteRoleEnum::Service:
      case VoteRoleEnum::NewMember:
      case VoteRoleEnum::Standards:
        return true;
      case VoteRoleEnum::Risk:
      case VoteRoleEnum::Webmaster:
      case VoteRoleEnum::Brotherhood:
      case VoteRoleEnum::Historian:
      default: 
        return false;
    }
  }
}
