<?hh // strict

class VoteBallotSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'VotingID' => ModelField::int_field('voting_id'),
      'VoteList' => ModelField::string_field('vote_list'),
    };
  }

  public function getTableName(): string {
    return 'vote_ballot';
  }

  public function getIdField(): string {
    return 'id';
  }
}
