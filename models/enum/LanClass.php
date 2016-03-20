<?hh // strict

enum LanClass : int as int {
  Founder = 0;
  Alpha = 1;
  Beta = 2;
  Gamma = 3;
  Delta = 4;
  Epsilon = 5;
}

class LanClassInfo {
  public static function getName(?int $value): string {
    if (is_null($value))
      return '';
    switch ($value) {
      case LanClass::Founder:
        return 'Founder';
      case LanClass::Alpha:
        return 'Alpha';
      case LanClass::Beta:
        return 'Beta';
      case LanClass::Gamma:
        return 'Gamma';
      case LanClass::Delta:
        return 'Delta';
      default:
        return '';
    }
  }
}
