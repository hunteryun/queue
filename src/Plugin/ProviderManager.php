<?php

namespace Hunter\queue\Plugin;

use Hunter\Core\Discovery\PluginDiscovery;

/**
 * Gathers the provider plugins.
 */
class ProviderManager implements ProviderManagerInterface {

  /**
   * The object that discovers plugins managed by this manager.
   */
  protected $discovery;

  /**
   * The root paths keyed by namespace to look for plugin implementations.
   */
  protected $namespaces;

  /**
   * Constructs a new video_embed plugin manager.
   */
  public function __construct() {
    $dirs = \Hunter::moduleHandler()->getModuleDirectories();
    $this->discovery = new PluginDiscovery($dirs);
  }

  /**
   * Gets the plugin discovery.
   */
  protected function getDiscovery() {
    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    $definitions = $this->getDiscovery()->findAll();
    return $definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function filterApplicableDefinitions($provider) {
    foreach ($this->getDefinitions() as $definition) {
      if($definition['id'] == $provider){
        return $definition;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function loadProvider($provider = '') {
    $queue_server = config()->get('queue_server');
    if(empty($provider)){
      $provider = $queue_server['driver'];
    }
    $definition = $this->filterApplicableDefinitions($provider);
    return $definition ? new $definition['class']($this->getDefinitions()) : FALSE;
  }

}
