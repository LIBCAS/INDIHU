<?php
    namespace WP\Models;

    use WP\Models\FileModel;
    use Nette\Forms\Form;
    use WP\Entities\UserExperience;

class UserExperienceModel extends WpPostModel{

        /** FileModel */
        private $fileModel;

        private $userExperienceModel;

        public function __construct(FileModel $fileModel){
            $this->fileModel = $fileModel;
        }
        
        public function getUserExperienceById(int $userExperienceId) : ?UserExperience{

            $item = $this->getWpPostById(UserExperience::POST_TYPE, $userExperienceId);

            if(!$item){
                return null;
            }

            if(isset($item->metaData['_thumbnail_id'])){
                $item->image = $this->fileModel->getFileById($item->metaData['_thumbnail_id']);
            }else{
                $item->image = null;
            }

            return UserExperience::map($item);
        }

        public function findUserExperiences(array $filter = [], array $sort = [], int $limit = 0, int $offset = 0) : array{
            $items = $this->findWpPosts($filter, $sort, $limit, $offset);

            foreach($items['items'] as &$item){
                if(isset($item->metaData['_thumbnail_id'])){
                    $item->coverImage = $this->fileModel->getFileById($item->metaData['_thumbnail_id']);
                }else{
                    $item->coverImage = null;
                }

                $item = UserExperience::map($item);
            }

            return $items;
        }

        public function createUserExperienceForm(?UserExperience $userExperience) : Form{
            $form = new Form();
            $projectId = '';
            $comment = '';

            if ($userExperience instanceof UserExperience) {
                $projectId = $userExperience->projectId;
                $comment = $userExperience->comment;
            }

            $projects = array_merge(['' => 'Zvolte projekt'], UserExperience::$projectList);

            $form->addSelect('project', 'Projekt:' , $projects)
                ->setPrompt('Zvolte projekt')->setDefaultValue($projectId);
            $form->addText('comment', 'Komentář:')->setDefaultValue($comment);

            return $form;
        }

        public function saveMetaBoxForm($data, $userExperienceId){
            global $wpdb;

            if(isset($data['project'])){
                update_post_meta($userExperienceId, 'IQ-projectId', $data['project']);
            }

            if(isset($data['comment'])){
                update_post_meta($userExperienceId, 'IQ-comment', $data['comment']);
            }
        }
    }
?>