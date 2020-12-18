<?php

namespace Drupal\ga_core\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Lorem\Env;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block for displaying the GSPC JSON Feed.
 *
 * @Block(
 *   id = "ga_gspc_json_feed",
 *   admin_label = @Translation("GSPC Properties for Sale and Sold"),
 *   category = @Translation("GA Blocks"),
 * )
 */
class GaGspcFeedBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The endpoint URL we are querying to pull For Sale results.
   */
  const GSPC_FOR_SALE_FEED_ENDPOINT = 'https://spc-reach.com/land-for-sale/export?_format=json';

  /**
   * The endpoint URL we are querying to pull Sold results.
   */
  const GSPC_SOLD_FEED_ENDPOINT = 'https://spc-reach.com/property-sold/export?_format=json';

  /**
   * The Guzzle HTTP Client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The logger channel factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client, LoggerChannelFactory $logger_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->logger = $logger_factory->get('ga_core');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Lorem\Exception\MissingSiteNameException
   */
  public function build() {
    if (Env::getSiteName() !== 'gspc') {
      return [];
    }
    return [
      '#theme' => 'gspc_json_feed_block',
      '#for_sale_items' => $this->getFeeds(self::GSPC_FOR_SALE_FEED_ENDPOINT),
      '#sold_items' => $this->getFeeds(self::GSPC_SOLD_FEED_ENDPOINT),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // This block should be cached for 1h.
    return 60 * 60;
  }

  /**
   * Helper function to retrieve data from endpoints.
   *
   * @param string $endpoint
   *   Endpoint to request for Feeds.
   *
   * @return array|mixed
   *   Return an array with feeds.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function getFeeds($endpoint) {
    try {
      $response = $this->httpClient->request('GET', $endpoint);
    }
    catch (\Exception $e) {
      $this->logger->warning("An error prevented fetching new feed items from the GSPC JSON Feed endpoint: {$endpoint}. Error message: {$e->getMessage()}");
    }

    if (empty($response) || $response->getStatusCode() != 200) {
      $this->logger->warning("The response received from the GSPC JSON Feed endpoint is empty or invalid.");
      return [];
    }

    $content = $response->getBody()->getContents();

    try {
      $data = Json::decode($content);
    }
    catch (\Exception $e) {
      $this->logger->warning("An error prevented parsing new feed items from the GSPC JSON Feed. Error message: {$e->getMessage()}");
    }

    if (empty($data)) {
      $this->logger->warning("The content received from the GSPC JSON Feed endpoint is empty.");
      return [];
    }

    return $data;
  }

}
