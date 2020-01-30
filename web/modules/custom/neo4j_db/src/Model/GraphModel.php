<?php

namespace Drupal\neo4j_db\Model;

class GraphModel extends AbstractModel {

  public function discover($name) {
    $nameSpace = (new \ReflectionObject($this))->getNamespaceName();
  }

}
