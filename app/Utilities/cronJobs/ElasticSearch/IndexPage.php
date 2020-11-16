<?php
    namespace WP\Utilities\CronJobs\ElasticSearch;

    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use WP\Models\PageModel;
    use WP\Services\ElasticSearch;
    
    class IndexPage extends Command{
    
        /** @var PageModel */
        private $pageModel;

        /** @var ElasticSearch */
        private $elasticSearch;

        public function __construct(PageModel $pageModel, ElasticSearch $elasticSearch){
            parent::__construct();
            $this->pageModel = $pageModel;
            $this->elasticSearch = $elasticSearch;
        }
    
        protected function configure(): void{
            $this->setName('elastic:indexPage')
                ->setDescription('scripts reindex all pages');
        }
    
        protected function execute(InputInterface $input, OutputInterface $output): int{
           
            $this->elasticSearch->deleteType(ElasticSearch::TYPE_PAGE);

            $args = [
                'type' => 'page',
                'status' => 'publish'
            ];

            $pages = $this->pageModel->findPages($args);

            foreach($pages['items'] as $page){
                $this->elasticSearch->index($page->toElastic(), $page->id, ElasticSearch::TYPE_PAGE);
            }

            $output->writeLn('page index done');
            return 0;
        }
    }
?>