<?php

namespace Hunter\queue\Plugin;

/**
 * Interface for the class that gathers the provider plugins.
 */
interface ProviderManagerInterface {

  /**
   * Get the provider applicable to the given user input.
   */
  public function filterApplicableDefinitions($provider);

  /**
   * Load a provider from user input.
   */
  public function loadProvider($provider);
}
