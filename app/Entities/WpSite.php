<?php 
    namespace WP\Entities;
    
    /**
     * @property-read string $blogName
     * @property-read string $charset
     * @property-read string $description
     * @property-read string $language
     * @property-read string $homeUrl
     * @property-read string $siteUrl
     * @property-read string $rss
     */
    class WpSite{
        
        use \Nette\SmartObject;

        /** @var string */
        private $blogName;

        /** @var string */
        private $charset;

        /** @var string */
        private $description;

        /** @var string */
        private $language;

        /** @var string */
        private $homeUrl;

        /** @var string */
        private $siteUrl;

        /** @var string */
        private $rss;

        public function __construct(){
            $this->blogName = get_bloginfo('name');
            $this->charset = get_bloginfo('charset');
            $this->description = get_bloginfo('description');
            $this->language = get_bloginfo('language');
            /* Polylang */
            if(function_exists("pll_home_url") && function_exists("pll_current_language")){
                $this->homeUrl = pll_home_url(pll_current_language());
            }else{ 
                $this->homeUrl = home_url();
            }
            $this->siteUrl = site_url();
            $this->rss = get_bloginfo('rss_url');
        }

        /**
         * @return string
         */
        public function getBlogName() : string{
            return $this->blogName;
        }

        /**
         * @return string
         */
        public function getCharset() : string{
            return $this->charset;
        }

        /**
         * @return string
         */
        public function getDescription() : string{
            return $this->description;
        }

        /**
         * @return string
         */
        public function getLanguage() : string{
            return $this->language;
        }

        /**
         * @return string
         */
        public function getHomeUrl() : string{
            return $this->homeUrl;
        }

        /**
         * @return string
         */
        public function getSiteUrl() : string{
            return $this->siteUrl;
        }

        /**
         * @return string
         */
        public function getRss() : string{
            return $this->rss;
        }
    }
?>