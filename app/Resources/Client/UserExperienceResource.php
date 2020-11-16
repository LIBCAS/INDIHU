<?php
    namespace WP\Client\Resources;

    use WP\Entities\UserExperience;
    use WP\Models\UserExperienceModel;

class UserExperienceResource extends Base{

        /** @var UserExperienceModel */
        private $userExperienceModel;

        /**
         * @param UserExperienceModel $userExperienceModel
         */
        public function __construct(UserExperienceModel $userExperienceModel){
            $this->userExperienceModel = $userExperienceModel;
        }

        public function blockSlider(array $params) : string{

            $args = [
                'type' => 'user_experience',
                'status' => 'publish'
            ];

            $projects = UserExperience::$projectList;
            $userExperiences = $this->userExperienceModel->findUserExperiences($args, ['rand' => '']);

            $param = [
                'userExperiences' => $userExperiences,
                'projects' => $projects
            ];

            return $this->renderShortcode(CLIENT_TEMPLATE_DIR . '/userExperience/slider.latte', $param);

        }

        // public function actionHtmlGetUserExperiences(){
        //     $args = [
        //         'type' => 'user_experience',
        //         'status' => 'publish'
        //     ];

        //     $userExperiences = $this->userExperienceModel->findUserExperiences($args);

        //     $param = [
        //         'userExperiences' => $userExperiences
        //     ];

        //     $response = [
        //         'html' => PageRender::renderToString(CLIENT_TEMPLATE_DIR . '/userExperience/_parts/locations.latte', $param)
        //     ];
            
        //     $this->sendJsonData($response);
        // }
    }
?>