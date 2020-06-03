<?php

namespace Drupal\nlc_topics\Model\TaxonomyTerm;

use Drupal\neo4j_db_entity\Model\TaxonomyTerm\AbstractGraphEntityTaxonomyTermModel;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class GraphEntityLibraryLabelModel
 *
 * @package Drupal\nlc_topics\Model\TaxonomyTerm
 *
 * @OGM\Node(label="LibraryLabel")
 */
class GraphEntityLibraryLabelModel extends AbstractGraphEntityTaxonomyTermModel {

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $bundle = 'library_label';

  protected $type = 'LibraryLabel';

}
