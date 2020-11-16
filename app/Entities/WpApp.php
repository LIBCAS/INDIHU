<?php
    namespace WP\Entities;

    use WP\Entities\WpSite;
    use WP\Entities\WpPost;
    use WP\Entities\WpUser;
    use WP\Entities\WpBreadcrumbs;

    /**
     * @property-read WpSite $site
     * @property-read string $title
     * @property-read WpUser $user
     * @property-read WpBreadcrumbs $breadcrumbs
     */
    class WpApp{

        use \Nette\SmartObject;

        /** @var string */
        private $site;

        /** @var string */
        private $title;

        /** @var WpUser */
        private $user;

        /**
         * @var WpBreadcrumbs
         */
        private $breadcrumbs;

        public function __construct(){
            $this->site = new WpSite();
            $this->title = trim(wp_title(' ', false, 'left'));
            if(is_user_logged_in()){
                $this->user = new WpUser();
            }else{
                $this->user = null;
            }
            $this->breadcrumbs = new WpBreadcrumbs();
        }

        /**
         * @return string
         */
        public function getSite() : WpSite{
            return $this->site;
        }

        /**
         * @return string
         */
        public function getTitle() : string{
            return $this->title;
        }

        /**
         * @return WpUser
         */
        public function getUser() : WpUser{
            return $this->user;
        }

        /**
         * @return WpPost
         */
        public function getPost() : WpPost{
            return $this->post;
        }

        /**
         * @return WpBreadcrumbs
         */
        public function getBreadcrumbs() : WpBreadcrumbs{
            return $this->breadcrumbs;
        }

    }
?>