<?php

namespace Hunter\queue\Annotation;

use Hunter\Core\Annotation\Plugin;

/**
 * Defines a QueueProvider item annotation object.
 *
 * @Annotation
 */
class QueueProvider extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The title of the plugin.
   *
   */
  public $title;

  /**
   * The type of the plugin.
   *
   */
  public $type;
}
