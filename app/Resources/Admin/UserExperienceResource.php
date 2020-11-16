<?php
    namespace WP\Admin\Resources;

    use WP\Models\UserExperienceModel;
    use WP\Utilities\ArrayFormat;

    class UserExperienceResource extends Base{

        /** @var UserExperienceModel */
        private $userExperienceModel;

        public function __construct(UserExperienceModel $userExperienceModel){
            $this->userExperienceModel = $userExperienceModel;
        }

        public function metaBoxForm(\WP_Post $post){

            $userExperience = $this->userExperienceModel->getUserExperienceById($post->ID);
            
            $param = [
                'form' => $this->userExperienceModel->createUserExperienceForm($userExperience),
            ];

            echo $this->renderShortcode(ADMIN_TEMPLATE_DIR . '/userExperience/metaBox/form.latte', $param);
        }

        public function saveMetaBoxForm(int $postId, \WP_Post $post, bool $update){
            if($update && $post->post_status != "trash"){
                $this->userExperienceModel->saveMetaBoxForm($_POST, $postId);
            }
        }
    }

?>