<?php

require 'vendor/autoload.php';
require 'scripts/URIMapGenerator.php';
require 'scripts/ModelGeneratorTask.php';

class RoboFile extends \Robo\Tasks {

  use URIGenerator;
  use ModelGeneratorTrait;

  function build() {
    $this->taskGenURIMap()->run();
    $this->taskGenModels()->run();
  }
}
