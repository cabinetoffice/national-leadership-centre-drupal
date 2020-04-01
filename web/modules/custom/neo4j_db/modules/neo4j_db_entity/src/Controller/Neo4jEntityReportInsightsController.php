<?php


namespace Drupal\neo4j_db_entity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Neo4jEntityReportInsightsController extends ControllerBase {

  /**
   * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface
   */
  protected $graphEntityModelManager;

  /**
   * Constructs a new Neo4jEntityInsights controller.
   *
   * @param \Drupal\neo4j_db_entity\Model\GraphEntityModelManagerInterface $graphEntityModelManager
   */
  public function __construct(GraphEntityModelManagerInterface $graphEntityModelManager) {
    $this->graphEntityModelManager = $graphEntityModelManager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('neo4j_db_entity.model.manager')
    );
  }

  public function reportPlugins() {
    $build = $rows = [];

    // ENTITY MODELS
    $build['entity_models'] = [
      '#type' => 'table',
      '#header' => [t('Type'), t('Entity'), t('Bundle'), t('Class')],
      '#empty' => t('There are no entity models.')
    ];

    foreach ($this->graphEntityModelManager->getEntityModels() as $type => $entityModels) {
      $build[$type] = [
        '#type' => 'container',
      ];
      /**
       * @var string $bundle
       * @var \Drupal\neo4j_db_entity\Model\GraphEntityModelInterface $entityModel
       */
      foreach ($entityModels as $bundle => $entityModel) {
        $itemName = "{$entityModel->entityType()}_{$entityModel->bundle()}";
        $rows["{$type}_{$bundle}"] = [
          $itemName,
          $entityModel->entityType(),
          $entityModel->bundle(),
          get_class($entityModel),
        ];
      }
      $build['entity_models']['#rows'] = $rows;
    }

    return $build;
  }
}
