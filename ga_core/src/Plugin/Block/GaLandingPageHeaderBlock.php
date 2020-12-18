<?php

namespace Drupal\ga_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block for displaying a Landing Page Header.
 *
 * This will bundle together 3 fields of the landing page being viewed: the
 * "Label", the "Title" and the "Summary".
 *
 * @Block(
 *   id = "ga_landing_page_header",
 *   admin_label = @Translation("GA - Landing Page Header"),
 *   category = @Translation("GA Blocks"),
 *   context = {
 *     "node" = @ContextDefinition("entity:node", required = TRUE, label = @Translation("Node"))
 *   }
 * )
 */
class GaLandingPageHeaderBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new AlertBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#theme' => 'landing_page_header',
      '#eyebrow' => '',
      '#title' => '',
      '#summary' => '',
    ];

    // The eyebrow should display the "Label" field if it has a value, or
    // "Guide" if it's empty AND the landing page is of type "Guide".
    if (!empty($route_node->field_landing_page_label->value)) {
      $build['#eyebrow'] = $route_node->field_landing_page_label->value;
    }
    elseif (!empty($route_node->field_landing_page_type->value) && $route_node->field_landing_page_type->value === 'guide') {
      $build['#eyebrow'] = $this->t('Guide');
    }

    $build['#title'] = $route_node->getTitle();
    $summary_display_options = [
      'label' => 'hidden',
      'type' => 'text_default',
    ];
    $build['#summary'] = $this->entityTypeManager->getViewBuilder('node')
      ->viewField($route_node->field_summary, $summary_display_options);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }

}
