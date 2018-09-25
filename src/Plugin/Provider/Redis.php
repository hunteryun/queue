<?php

namespace Hunter\queue\Plugin\Provider;

use Hunter\queue\Annotation\QueueProvider;
use Enqueue\Redis\RedisConnectionFactory;

/**
 * @QueueProvider(
 *   id = "redis",
 *   title = "Redis",
 *   type = "queue_provider"
 * )
 */
class Redis {

    /**
     * The queue.
     *
     * @var string
     */
    protected $queue;

    /**
     * The plugins.
     *
     * @var string
     */
    protected $pluginList;

    /**
     * The psrContext.
     */
    protected $psrContext;

    /**
     * The consumer.
     */
    protected $consumer;

    /**
     * Create a queue.
     */
    public function __construct($pluginList = array()) {
      if(!empty($pluginList)){
        $this->pluginList = $pluginList;
      }
      if(empty($this->queue)){
        $this->createQueue();
      }
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($job, $data) {
      $data['job'] = $job;
      $message = $this->psrContext->createMessage($data);
      $this->psrContext->createProducer()->send($this->queue, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function receiveItem($parms) {
      $this->consumer = $this->psrContext->createConsumer($this->queue);
      $message = $this->consumer->receive($parms['delay']);
      $data = $message->getBody();
      if(isset($data['job']) && isset($this->pluginList[$data['job']])){
        $class = $this->pluginList[$data['job']]['class'];
        $handle = new $class();
        $response = $handle->processItem($data);
      }else {
        $response = $data;
      }
      //$this->queue->deleteItem($data);
      $this->consumer->acknowledge($message);
      return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueue() {
      global $queue_server;
      $connectionFactory = new RedisConnectionFactory([
          'host' => 'localhost',
          'port' => 6379,
          'vendor' => 'predis',
      ]);

      $this->psrContext = $connectionFactory->createContext();
      $this->queue = $this->psrContext->createQueue($queue_server['queue']);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteQueue() {
      $this->psrContext->deleteQueue($this->queue);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($item) {
    }

}
