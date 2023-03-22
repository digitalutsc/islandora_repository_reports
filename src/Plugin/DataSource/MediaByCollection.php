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
    $media_storage = $entity_type_manager->getStorage('media');
    $result = $media_storage->getAggregateQuery()
      ->groupBy('field_media_of')
      ->aggregate('field_media_of', 'COUNT')
      ->execute();
    $media_counts = [];
    foreach ($result as $collection) {
      if (!is_null($collection['field_media_of_target_id'])) {
        if ($collection_node = \Drupal::entityTypeManager()->getStorage('node')->load($collection['field_media_of_target_id'])) {
          if ($utilities->nodeIsCollection($collection_node)) {
            $media_counts[$collection_node->getTitle()] = $collection['field_media_of_count'];
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
