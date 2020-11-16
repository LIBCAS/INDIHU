<?php
    namespace WP\Entities;

    use WP\Entities\District;

    /**
     * @property string $projectId
     * @property string $comment
     * @property WpFile $image
     */
    class UserExperience extends WpPost{

        use \Nette\SmartObject;

        const POST_TYPE = 'user_experience';

        public static $projectList = [
            0 => 'Index',
            1 => 'Mind',
            2 => 'Exhibition',
            3 => 'OCR',
        ];

        /** @var string */
        private $projectId;

        /** @var string */
        private $comment;

        /** @var WpFile */
        private $image = null;

        /**
         * @return string
         */
        public function getProjectId() : string{
            return $this->projectId;
        }

        /**
         * @param string $projectId
         * @return void
         */
        public function setProjectId(string $projectId) : void{
            $this->projectId = $projectId;
        }

        /**
         * @return string
         */
        public function getProject() : string{
            return self::$projectList[$this->projectId];
        }

        /**
         * @return string
         */
        public function getComment() : string{
            return $this->comment;
        }

        /**
         * @param string $comment
         * @return void
         */
        public function setComment(string $comment) : void{
            $this->comment = $comment;
        }

        public function getImage() : ?WpFile{
            return $this->image;
        }

        public function setImage(?WpFile $image) : void{
            $this->image = $image;
        }

        public function jsonSerialize($type = null) : array{

            if($type == "geoJson"){
                return [
                   
                ];
            }

            return [
                'name' => $this->name
            ];
        }

        /**
         * @param \stdClass $array
         * @return UserExperience
         */
        public static function map(\stdClass $array) : UserExperience{
            $userExperience = new UserExperience();

            // post type
            $userExperience->setId($array->ID);
            $userExperience->setName($array->post_title);
            $userExperience->setContent($array->post_content);
            $userExperience->setSlug($array->post_name);
            $userExperience->setCreated($array->post_date);
            $userExperience->setModified($array->post_modified);
            
            // userExperience
            $userExperience->setProjectId($array->metaData['IQ-projectId'] ?? "");
            $userExperience->setComment($array->metaData['IQ-comment'] ?? "");

            // images
            $userExperience->setImage($array->coverImage);
            
            return $userExperience;
        }
    }
?>