<?php

namespace Hunter\queue\Plugin\Provider;

use Hunter\queue\Annotation\QueueProvider;

/**
 * @QueueProvider(
 *   id = "redis",
 *   title = "Redis",
 *   type = "queue_provider"
 * )
 */
class Redis {

  /**
   * {@inheritdoc}
   */
  public function createItem($data) {
    global $queue_server;
    $factory = new PheanstalkConnectionFactory([
        'host' => $queue_server['host'],
        'port' => $queue_server['port']
    ]);

    $psrContext = $factory->createContext();
    $fooQueue = $psrContext->createQueue($queue_server['queue']);
    $message = $psrContext->createMessage($data);

    $psrContext->createProducer()->send($fooQueue, $message);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteItem($item) {
  }

}
