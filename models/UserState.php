<?hh // strict

enum UserState : int as int {
	Applicant = 0;
	Pledge = 1;
	Member = 2;
	Disabled = 3;
	Inactive = 4;
	Alum = 5;
}