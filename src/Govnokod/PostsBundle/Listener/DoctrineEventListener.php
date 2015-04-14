<?php
namespace Govnokod\PostsBundle\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Govnokod\PostsBundle\Entity\Post;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

class DoctrineEventListener
{
    /**
     * @var \OldSound\RabbitMqBundle\RabbitMq\Producer
     */
    protected $indexProducer;

    public function __construct(Producer $indexProducer)
    {
        $this->indexProducer = $indexProducer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Post) {
            $this->addTaskToSolrQueue($entity, 'update');
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Post) {
            $this->addTaskToSolrQueue($entity, 'update');
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Post) {
            $this->addTaskToSolrQueue($entity, 'delete');
        }
    }

    protected function addTaskToSolrQueue(Post $post, $action)
    {
        try {
            $this->indexProducer->publish('post:' . $action . ':' . $post->getId());
        } catch (\Exception $e) {
        }
    }
}