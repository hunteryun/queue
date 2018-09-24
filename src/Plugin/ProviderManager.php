<?php

namespace Hunter\queue\Plugin;

/**
 * Gathers the provider plugins.
 */
class ProviderManager implements ProviderManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    global $app;
    $definitions = $app->getPluginList();
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
    global $queue_server;
    if(empty($provider)){
      $provider = $queue_server['driver'];
    }
    $definition = $this->filterApplicableDefinitions($provider);
    return $definition ? new $definition['class']($this->getDefinitions()) : FALSE;
  }

}
