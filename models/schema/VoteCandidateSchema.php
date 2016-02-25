<?hh // strict

class VoteCandidateSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'VoteRole' => ModelField::int_field('vote_role'),
      'UserID' => ModelField::int_field('user_id'),
      'Score' => ModelField::int_field('score'),
      'Description' => ModelField::string_field('description'),
      'VotingID' => ModelField::int_field('voting_id'),
    };
  }

  public function getTableName(): string {
    return 'vote_candidates';
  }

  public function getIdField(): string {
    return 'id';
  }
}
