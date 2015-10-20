<?hh

class ControllerConfig {
	private string $title = '';
	private Vector<UserState> $user_state = Vector{};
	private Vector<UserRoleEnum> $user_roles = Vector{};

	public function setUserState(Vector<UserState> $states): this {
		$this->user_state = $states;
		return $this;
	}

	public function getUserState(): Vector<UserState> {
		return $this->user_state;
	}

	public function setUserRoles(Vector<UserRoleEnum> $roles): this {
		$this->user_roles = $roles;
		return $this;
	}

	public function getUserRoles(): Vector<UserRoleEnum> {
		return $this->user_roles;
	}

	public function setTitle(string $title): this {
		$this->title = $title;
		return $this;
	}

	public function getTitle(): string {
		return $this->title;
	}
}