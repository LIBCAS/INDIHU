<?php
    namespace WP\Entities;

    use WP\Entities\Attributes\Perex;

    /**
     * @property WpFile $image
     * @property array $categories
     * @property array $tags
     * @property string $secretKey
     */
    class Post extends WpPost{

        use \Nette\SmartObject;

        const POST_TYPE = 'post';

        use Perex;

        /** @var WpFile */
        private $image = null;

        /** @var array */
        private $categories = [];
        
        /** @var array */
        private $tags = [];

        /** @var string */
        private $secretKey;

        /**
         * @return WpFile
         */
        public function getImage() : ?WpFile{
            return $this->image;
        }

        /**
         * @param WpFile|null $image
         * @return void
         */
        public function setImage(?WpFile $image)  : void{
            $this->image = $image;
        }

        /**
         * @return array
         */
        public function getCategories() : array{
            return $this->categories;
        }

        /**
         * @param PostCategory $category
         * @return void
         */
        public function addCategory(PostCategory $category) : void{
            $this->categories[] = $category;
        }

        /**
         * @param array $categories
         * @return void
         */
        public function setCategories(array $categories) : void{
            $this->categories = $categories;
        }
        
        /**
         * @return array
         */
        public function getTags() : array{
            return $this->tags;
        }

        /**
         * @param PostTag $category
         * @return void
         */
        public function addTag(PostTag $tag) : void{
            $this->tags[] = $tag;
        }

        /**
         * @param array $tags
         * @return void
         */
        public function setTags(array $tags) : void{
            $this->tags = $tags;
        }

        /**
         * @return string
         */
        public function getSecretKey() : string{
            return $this->secretKey;
        }

        /**
         * @param string $secretKey
         * @return void
         */
        public function setSecretKey(string $secretKey) : void{
            $this->secretKey = $secretKey;
        }

        /**
         * @return array
         */
        public function toElastic() : array{
            $elasticObject = [
                'type' => self::POST_TYPE,
                'suggest' => strip_tags($this->name),
                'title' => strip_tags($this->name),
                'description' => strip_tags($this->content),
                'date_publish' => $this->created,
                'url' => get_permalink($this->id),
                'tags' => array_map(function($tag){
                    return $tag->slug;
                }, $this->tags),
                'prosecution' => null,
                'prosecution_name' => null,
                'lang' => $this->lang,
            ];

            if(!empty($this->categories)){
                if($this->categories[0]->prosecution){
                    $elasticObject['prosecution'] = $this->categories[0]->prosecution->id;
                    $elasticObject['prosecution_name'] = $this->categories[0]->prosecution->name;
                }
            }

            return $elasticObject;
        }


        /**
         * @param \stdClass $array
         * @return void
         */
        public static function map(\stdClass $array) : Post{
            $post = new Post();

            // post type
            $post->setId($array->ID);
            $post->setName($array->post_title);
            $post->setContent($array->post_content);
            $post->setPerex($array->post_excerpt);
            $post->setImage($array->image);
            if(isset($array->taxonomies['category'])){
                foreach($array->taxonomies['category'] as $category){
                    $post->addCategory($category);
                }
            }
            if(isset($array->taxonomies['post_tag'])){
                foreach($array->taxonomies['post_tag'] as $tag){
                    $post->addTag($tag);
                }
            }
            $post->setSlug($array->post_name);
            $post->setCreated($array->post_date);
            $post->setModified($array->post_modified);

            if(function_exists("pll_get_post_language")){
                $post->setLang(pll_get_post_language($array->ID));
            }else{
                $post->setLang('cs');
            }
            
            $post->setSecretKey($array->metaData['IQ-secret_key'] ?? '');

            return $post;
        }
    }
?>