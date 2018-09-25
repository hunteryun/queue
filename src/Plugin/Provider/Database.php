<?php

namespace Hunter\queue\Plugin\Provider;

use Hunter\queue\Annotation\QueueProvider;
use Enqueue\Dbal\DbalConnectionFactory;

 /**
  * @QueueProvider(
  *   id = "database",
  *   title = "Database",
  *   type = "queue_provider"
  * )
  */
class Database {

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
      $message = $this->psrContext->createMessage(json_encode($data));
      $this->psrContext->createProducer()->send($this->queue, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function receiveItem($parms) {
      $this->consumer = $this->psrContext->createConsumer($this->queue);
      $message = $this->consumer->receive($parms['delay']);
      $data = json_decode($message->getBody(), true);
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
      global $queue_server, $databases;
      $factory = new DbalConnectionFactory('mysql://'.$databases['default']['username'].':'.$databases['default']['password'].'@'.$databases['default']['host'].':'.$databases['default']['port'].'/'.$databases['default']['database']);

      $this->psrContext = $factory->createContext();
      $this->psrContext->createDataBaseTable();
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
