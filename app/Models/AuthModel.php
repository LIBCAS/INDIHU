<?php
    namespace WP\Models;

    use WP\Utilities\ArrayFormat;
    use Nette\Forms\Form;

    class AuthModel extends BaseModel{

        public function __construct(){
            parent::__construct();
        }
        
        public function getAuthSettings() : array{
            return get_option('IQ-auth_settings', []);
        }

        public function createAuthorizationSettingsForm(array $pages, array $settings) : Form{
            $form = new Form();
            
            $form->addTextArea('ip_addresses', 'Autorizované IP adresy:', 0, 5);
            $form->addSelect('page', 'Hlavní stránka pro neautorizované uživatele:', ArrayFormat::editArrayFormatForSelect($pages))
                ->setPrompt('Zvolte stránku');
            
            if(!empty($settings)) {   
                $ipsString = '';
                $first = true;
                $ips = unserialize($settings['ips']);
                
                foreach($ips as $ip){
                    if(!$first){
                        $ipsString .= PHP_EOL;
                    }
                    $ipsString .= $ip;
                    $first = false;
                }

                $form->setDefaults([
                    "ip_addresses" => $ipsString,
                    "page" => $settings['page_id']
                ]);

            } 

            $form->addSubmit('send', 'Uložit')
                ->setAttribute('class', 'button');

            return $form;
        }

        public function updateAuthSettings(\Nette\Utils\ArrayHash $data) : bool{

            $ips = preg_split('/\r\n|\r|\n/', $data['ip_addresses']);
            $validIps = [];

            foreach($ips as $ip){
                if($ip != ''){
                    $validIps[] = $ip;
                }
            }
            
            $settings = [
                'ips' => serialize($validIps),
                'page_id' => $data['page']
            ];

            // dump($settings);die;
            
            return update_option('IQ-auth_settings', $settings);

        }

    }
?>