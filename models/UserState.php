<?hh // strict

enum UserState : int as int {
	Disabled = 3;
	Applicant = 0;
	Candidate = 6;
	Pledge = 1;
	Active = 2;
	Inactive = 4;
	Alum = 5;
}