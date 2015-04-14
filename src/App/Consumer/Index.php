<?php
namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Index implements ConsumerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Nelmio\SolariumBundle\ClientRegistry
     */
    protected $solariumClientRegistry;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        gc_enable();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $em
            ->getConnection()
            ->getConfiguration()
            //->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
            ->setSQLLogger(null)
        ;

        $this->container = $container;

        $this->solariumClientRegistry = $this->container->get('solarium.client_registry');

        $kernel = $container->get('kernel');

        $logger = new \Monolog\Logger('index');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler('php://output', \Monolog\Logger::DEBUG));

        $this->logger = $logger;
        $this->logger->addInfo(sprintf('Consume %s on "%s:%s" for the %s environment with debug %s', 'index', $container->getParameter('rabbitmq_host'), $container->getParameter('rabbitmq_port'), $kernel->getEnvironment(), var_export($kernel->isDebug(), true)));
    }

    public function __destruct()
    {
        $this->logger->addInfo('Consume close');
    }

    public function execute(AMQPMessage $msg)
    {
        $start_time = microtime(true);

        $completed = true;

        $entity_name = null;
        $action = null;
        $payload = null;
        if (stripos($msg->body, 'post:update:') === 0) {
            $entity_name = 'post';
            $action = 'update';
            $payload = substr($msg->body, 12);
        } else if (stripos($msg->body, 'post:delete:') === 0) {
            $entity_name = 'post';
            $action = 'delete';
            $payload = substr($msg->body, 12);
        }

        switch ($entity_name) {
            case 'post':
                $solrClient = $this->solariumClientRegistry->getClient();

                switch ($action) {
                    case 'update':
                        /** @var \Doctrine\ORM\EntityManager $em */
                        $em = $this->container->get('doctrine.orm.entity_manager');

                        $posts_ids = explode(',', $payload);

                        $posts_ids_count = count($posts_ids);
                        $task_status = $posts_ids_count . ' codes';

                        if ($posts_ids_count <= 10) {
                            $task_status .= ' (' . implode(', ', $posts_ids) . ')';
                        }

                        $this->writeln('New update task for ' . $task_status);

                        $query_start_time = microtime(true);

                        $query = $em->createQuery('SELECT c FROM GovnokodPostsBundle:Post c WHERE c.id IN (?1)');
                        $query->setParameter(1, $posts_ids);

                        try {
                            $posts = $query->getResult();
                            $this->writeln('Query done in ' . round(microtime(true) - $query_start_time, 5) . ' sec.');
                        } catch (\PDOException $e) {
                            $this->writeln('Query error. message: ' . $e->getMessage());
                            $posts = null;
                        }

                        /** @var \Govnokod\PostsBundle\Entity\Post[] $posts */
                        if ($posts) {
                            $updateQuery = $solrClient->createUpdate();

                            foreach ($posts as $post) {
                                $common_data = array();
                                $common_data[] = $post->getId();

                                $tags = $post->getTags();

                                $document = new \Solarium\QueryType\Update\Query\Document\Document([
                                    'id'            =>    'post-' . $post->getId(),
                                    'type'          =>    'post',
                                    'createdAt'     =>    $updateQuery->getHelper()->formatDate($post->getCreatedAt()),
                                    'updatedAt'     =>    $updateQuery->getHelper()->formatDate($post->getUpdatedAt()),
                                    'tags_slc_m'    =>    $tags
                                ]);

                                if ($post->getUser()) {
                                    $document['user_id_i'] = $post->getUser()->getId();
                                }

                                $common_data = array_unique($common_data);
                                $common_search_string = implode('          ', $common_data);

                                $document['common'] = $common_search_string;

                                $updateQuery->addDocument($document);
                            }

                            $update_ok = false;

                            $tries = 3;
                            for ($try = 1; $try <= $tries; $try++) {
                                try {
                                    $result = $solrClient->update($updateQuery);
                                    $this->writeln('Update try ' . $try . ' done, status: ' . $result->getStatus() . ', QTime: ' . $result->getQueryTime());
                                    $update_ok = true;
                                    break;
                                } catch(\Solarium\Exception\HttpException $e) {
                                    $this->writeln('Update try ' . $try . ' error: ' . $e->getMessage());
                                }
                            }

                            if (!$update_ok) {
                                $completed = false;
                            }
                        } else {
                            $this->writeln('No posts');
                        }

                        $em->clear();
                        $em->getConnection()->close();
                        break;

                    case 'delete':
                        $posts_ids = explode(',', $payload);
                        $this->writeln('New delete task for ' . count($posts_ids) . ' posts');

                        $updateQuery = $solrClient->createUpdate();
                        $updateQuery->addDeleteByIds($posts_ids);

                        $update_ok = false;

                        $tries = 3;
                        for ($try = 1; $try <= $tries; $try++) {
                            try {
                                $result = $solrClient->update($updateQuery);
                                $this->writeln('Update try ' . $try . ' done, status: ' . $result->getStatus() . ', QTime: ' . $result->getQueryTime());
                                $update_ok = true;
                                break;
                            } catch(\Solarium\Exception\HttpException $e) {
                                $this->writeln('Update try ' . $try . ' error: ' . $e->getMessage());
                            }
                        }

                        if (!$update_ok) {
                            $completed = false;
                        }
                        break;

                    default:
                        $this->writeln('Unknown action task');
                        break;
                }
                break;
        }

        gc_collect_cycles();

        if ($completed) {
            $this->writeln('Task completed in ' . round(microtime(true) - $start_time, 5) . ' sec.', true);
        } else {
            $this->writeln('Task not completed', true);
            return false;
        }
    }

    protected function writeln($text, $write_memory_usage = false, Array $context = [])
    {
        /*
        echo date('c'), ' ', $text;
        if ($write_memory_usage) {
            echo ' (memory: ', $this->formatBytes(memory_get_usage(true)), ')';
        }
        echo "\n";
        */

        if ($write_memory_usage && !isset($context['memory'])) {
            $context['memory'] = $this->formatBytes(memory_get_usage(true));
        }

        $this->logger->addInfo($text, $context);
    }

    protected function formatBytes($bytes)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2).' '.$unit[$i];
    }
}