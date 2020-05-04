<?php

namespace Drupal\neo4j_db\Model;

class GraphModel extends AbstractModel implements GraphModelInterface {

  public function discover($name) {
    $nameSpace = (new \ReflectionObject($this))->getNamespaceName();
  }

  public function modelPersist() {
    // TODO: Implement modelPersist() method.
  }

}
