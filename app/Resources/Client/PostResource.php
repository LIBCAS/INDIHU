<?php
    namespace WP\Client\Resources;

    use WP\Utilities\PageRender;
    use WP\Models\PostModel;
    use Nette\Utils\Paginator;

class PostResource extends Base{

        /** @var PostModel */
        private $postModel;

        /**
         * @param PostModel $postModel
         */
        public function __construct(PostModel $postModel){
            $this->postModel = $postModel;
        }
    }
?>