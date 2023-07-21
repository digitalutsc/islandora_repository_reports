<?php

namespace Drupal\islandora_repository_reports\Plugin\DataSource;

/**
 * Data source that gets File counts by MIME type.
 */
class MimeType implements IslandoraRepositoryReportsDataSourceInterface {

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
    return t('Files by MIME type');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseEntity() {
    return 'file';
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
    return t('@total files grouped by MIME type.', ['@total' => $total]);
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    $utilities = \Drupal::service('islandora_repository_reports.utilities');
    $entity_type_manager = \Drupal::service('entity_type.manager');
    $file_storage = $entity_type_manager->getStorage('file');
    $result = $file_storage->getAggregateQuery()
      ->groupBy('filemime')
      ->aggregate('filemime', 'COUNT')
      ->execute();
    $format_counts = [];
    foreach ($result as $format) {
      $format_counts[$format['filemime']] = $format['filemime_count'];
    }

    $this->csvData = [[t('MIME type'), 'Count']];
    foreach ($format_counts as $type => $count) {
      $this->csvData[] = [$type, $count];
    }

    return $format_counts;
  }

}
