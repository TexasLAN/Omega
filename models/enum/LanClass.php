<?hh // strict

enum LanClass : int as int {
  Founder = 0;
  Alpha = 1;
  Beta = 2;
  Gamma = 3;
  Delta = 4;
  Epsilon = 5;
  Zeta = 6;
  Eta = 7;
  Theta = 8;
  Iota = 9;
  Kappa = 10;
  Lambda = 11;
  Mu = 12;
  Nu = 13;
  Xi = 14;
  Omicron = 15;
  Pi = 16;
  Rho = 17;
  Sigma = 18;
  Tau = 19;
  Upsilon = 20;
  Phi = 21;
  Chi = 22;
  Psi = 23;
  Omega = 24;
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
