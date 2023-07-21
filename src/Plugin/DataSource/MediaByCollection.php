<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source that gets media counts by Islandora Collection.
 */
class MediaByCollection implements IslandoraRepositoryReportsDataSourceInterface {

  /**
   * An array of arrays corresponding to CSV records.
   *
   * @var string
   */
  public $csvData;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return t('Media by Islandora Collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseEntity() {
    return 'media';
  }

  /**
   * {@inheritdoc}
   */
  public function getChartType() {
    return 'pie';
  }

  /**
   * {@inheritdoc}
   */
  public function getChartTitle($total) {
    return t('@total media, grouped by Islandora Collection.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');

    $entity_type_manager = \Drupal::service('entity_type.manager');
    $node_storage = $entity_type_manager->getStorage('node');
    $results = $node_storage->getAggregateQuery()
      ->groupBy('nid')
      ->aggregate('field_islandora_object_media', 'COUNT')
      ->condition('type', $utilities->getSelectedContentTypes(), 'IN')
      ->execute();
    $media_counts = [];
    
    foreach ($results as $result) {
      if (!is_null($result['nid'])) {
        if ($node = \Drupal::entityTypeManager()->getStorage('node')->load($result['nid'])) {
          if ($utilities->nodeIsCollection($node)) {
            $media_counts[$node->getTitle()] = $result['field_islandora_object_media_count'];

            // Get all child nodes belonging to this collection
            $children = $utilities->getDescendants($result['nid']);

            // Sum up the media_of counts for all children
            foreach ($children as $child_id) {
              $child_result = array_search($child_id, array_column($result, 'nid'));
              $media_counts[$node->getTitle()] += $results[$child_result]['field_islandora_object_media_count'];
            }
          }
        }
      }
    }

    $this->csvData = [[t('Collection'), 'Count']];
    foreach ($media_counts as $collection => $count) {
      $this->csvData[] = [$collection, $count];
    }

    return $media_counts;
  }

}
