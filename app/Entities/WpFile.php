<?php 
    namespace WP\Entities;

    /**
     * @property integer $id
     * @property string $title
     * @property string $type
     * @property string $url
     * @property string $extension
     * @property string $filesize
     * @property array $sizes
     * @property integer $cover
     */
    class WpFile{
        use \Nette\SmartObject;
        
        /** @var int */
        private $id;

        /** @var string */
        private $title;

        /** @var string */
        private $type;

        /** @var string */
        private $url;

        /** @var string */
        private $extension;

        /** @var string */
        private $filesize;

        /** @var array */
        private $sizes;

        /** @var bool */
        private $cover;

        /**
         * @param integer $id
         * @return void
         */
        public function setId(int $id) : void{
            $this->id = $id;
        }

        /**
         * @return integer
         */
        public function getId() : int{
            return $this->id;
        }

        /**
         * @param string $title
         * @return void
         */
        public function setTitle(string $title) : void{
            $this->title = $title;
        }

        /**
         * @return string
         */
        public function getTitle() : string{
            return $this->title;
        }

        /**
         * @param string $type
         * @return void
         */
        public function setType(string $type) : void{
            $this->type = $type;
        }

        /**
         * @return string
         */
        public function getType() : string{
            return $this->type;
        }

        /**
         * @param string $url
         * @return void
         */
        public function setUrl(string $url) : void{
            $this->url = $url;
        }

        /**
         * @return string
         */
        public function getUrl() : string{
            return $this->url;
        }

        /**
         * @param string $extension
         * @return void
         */
        public function setExtension(string $extension) : void{
            $this->extension = $extension;
        }

        /**
         * @return string
         */
        public function getExtension() : string{
            return $this->extension;
        }

        /**
         * @param string $filesize
         * @return void
         */
        public function setFilesize(string $filesize) : void{
            $this->filesize = $filesize;
        }

        /**
         * @return string
         */
        public function getFilesize() : string{
            return $this->filesize;
        }

        /**
         * @param array $sizes
         * @return void
         */
        public function setSizes(array $sizes) : void{
            $this->sizes = $sizes;
        }

        /**
         * @return array
         */
        public function getSizes() : array{
            return $this->sizes;
        }

        /**
         * @param boolean $cover
         * @return void
         */
        public function setCover(bool $cover) : void{
            $this->cover = $cover;
        }

        /**
         * @return boolean
         */
        public function isCover() : bool{
            return $this->cover;
        }

        /**
         * @return array
         */
        public function jsonSerialize($type = null) : array{
            return [
                'title' => $this->title,
                'type' => $this->type,
                'url' => $this->url,
                'sizes' => $this->sizes,
            ];
        }

        /**
         * @param \stdClass $array
         * @return WpFile
         */
        public static function map(\stdClass $array) : WpFile{
            $file = new WpFile();

            $file->setId($array->ID);                      
            $file->setTitle($array->post_title);
            $file->setType($array->post_mime_type);
            $file->setUrl($array->guid);
            $file->setExtension(wp_check_filetype($array->guid)['ext']);
            
            $fileSize = filesize(get_attached_file($array->ID)) / 1024;
            $file->setFilesize(round($fileSize > 1024 ? $fileSize/1024 : $fileSize, 2) . ($fileSize > 1024 ? ' MB' : ' kB'));

            if(isset($array->metadata['_wp_attachment_metadata'])){
                $file->setSizes(self::prepareSizes(unserialize($array->metadata['_wp_attachment_metadata'])));
            }else{
                $file->setSizes([]);
            }
            $file->setCover($array->cover ?? false);

            return $file;
        }

        /**
         * @param array $data
         * @return array
         */
        private static function prepareSizes(array $data) : array{
            $explodePath = explode('/', $data['file']);
            $folderPath = get_site_url() . '/wp-content/uploads/' . $explodePath[0] . '/' . $explodePath[1] . '/';

            foreach($data['sizes'] as $name => &$size){
                $size['file'] = $folderPath . $size['file'];
                unset($size['mime-type']);
            }

            $full = [
                'file' => get_site_url() . '/wp-content/uploads/' . $data['file'],
                'width' => $data['width'],
                'height' => $data['height']
            ];
            $data['sizes']['full'] = $full;

            return $data['sizes'];
        }

    }
?>