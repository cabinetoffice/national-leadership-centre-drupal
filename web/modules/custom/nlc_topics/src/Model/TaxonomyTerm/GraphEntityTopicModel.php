<?php

namespace Drupal\nlc_topics\Model\TaxonomyTerm;

use Drupal\neo4j_db_entity\Model\TaxonomyTerm\AbstractGraphEntityTaxonomyTermModel;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * Class GraphEntityTopicModel
 *
 * @package Drupal\nlc_topics\Model\TaxonomyTerm
 *
 * @OGM\Node(label="Topic")
 */
class GraphEntityTopicModel extends AbstractGraphEntityTaxonomyTermModel {

  /**
   * @var string
   *
   * @OGM\Property(type="string")
   */
  protected $bundle = 'topic';

  protected $type = 'Topic';

}
