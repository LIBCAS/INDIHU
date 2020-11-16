<?php
    namespace WP\Utilities\CronJobs\ElasticSearch;

    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use WP\Models\PostModel;
    use WP\Services\ElasticSearch;
    
    class IndexPost extends Command{
    
        /** @var PostModel */
        private $postModel;

        /** @var ElasticSearch */
        private $elasticSearch;

        public function __construct(PostModel $postModel, ElasticSearch $elasticSearch){
            parent::__construct();
            $this->postModel = $postModel;
            $this->elasticSearch = $elasticSearch;
        }
    
        protected function configure(): void{
            $this->setName('elastic:indexPost')
                ->setDescription('scripts reindex all posts');
        }
    
        protected function execute(InputInterface $input, OutputInterface $output): int{

            $this->elasticSearch->deleteType(ElasticSearch::TYPE_POST);

            $args = [
                'type' => 'post',
                'status' => 'publish'
            ];

            $posts = $this->postModel->findPosts($args);

            foreach($posts['items'] as $post){
                $this->elasticSearch->index($post->toElastic(), $post->id, ElasticSearch::TYPE_POST);

            }

            $output->writeLn('post index done');
            return 0;
        }
    }
?>