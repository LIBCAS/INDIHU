<?php
    namespace WP\Entities;

    use WP\Entities\Attributes\Perex;

    /**
     * @property WpFile $image
     * @property array $categories
     */
    class Page extends WpPost{

        use \Nette\SmartObject;

        const POST_TYPE = 'page';

        use Perex;

        /** @var WpFile */
        private $image;

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
        public function setImage(?WpFile $image) : void{
            $this->image = $image;
        }
        
        /**
         * @return array
         */
        public function toElastic() : array{
            return [
                'type' => self::POST_TYPE,
                'suggest' => strip_tags($this->name),
                'title' => strip_tags($this->name),
                'description' => strip_tags($this->content),
                'date_publish' => $this->created,
                'url' => get_permalink($this->id),
                'lang' => $this->lang,
            ];
        }

        /**
         * @param \stdClass $array
         * @return void
         */
        public static function map(\stdClass $array) : Page{
            $page = new Page();

            // post type
            $page->setId($array->ID);
            $page->setName($array->post_title);
            $page->setContent($array->post_content);
            $page->setPerex($array->post_excerpt);
            $page->setImage($array->image);
            $page->setSlug($array->post_name);
            $page->setCreated($array->post_date);
            $page->setModified($array->post_modified);
            
            if(function_exists("pll_get_post_language")){
                $page->setLang(pll_get_post_language($array->ID));
            }else{
                $page->setLang('cs');
            }

            return $page;
        }
    }
?>