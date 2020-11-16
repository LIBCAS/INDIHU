<?php
    namespace WP\Entities;
    
    /**
     * @property int $id
     * @property string $login
     * @property string $email
     * @property string $nickName
     * @property string $firstName
     * @property string $lastName
     * @property array $roles
     */
    class WpUser{

        use \Nette\SmartObject;

        /** @var int */
        private $id;

        /** @var string */
        private $login;

        /** @var string */
        private $email;

        /** @var string */
        private $nickName;

        /** @var string */
        private $firstName;

        /** @var string */
        private $lastName;

        /** @var array */
        private $roles;

        /** @return integer */
        public function getId() : int{
            return $this->id;
        }

        /**
         * @param integer $id
         * @return void
         */
        public function setId(int $id) : void{
            $this->id = $id;
        }

        /**
         * @return string
         */
        public function getLogin() : string{
            return $this->login;
        }

        /**
         * @param string $login
         * @return void
         */
        public function setLogin(string $login) : void{
            $this->login = $login;
        }

        /**
         * @return string
         */
        public function getEmail() : string{
            return $this->email;
        }

        /**
         * @param string $email
         * @return void
         */
        public function setEmail(string $email) : void{
            $this->email = $email;
        }

        /**
         * @return string
         */
        public function getNickName() : string{
            return $this->nickName;
        }

        /**
         * @param string $nickName
         * @return void
         */
        public function setNickName(string $nickName) : void{
            $this->nickName = $nickName;
        }

        /**
         * @return string
         */
        public function getFirstName() : string{
            return $this->firstName;
        }

        /**
         * @param string $firstName
         * @return void
         */
        public function setFirstName(string $firstName) : void{
            $this->firstName = $firstName;
        }

        /**
         * @return string
         */
        public function getLastName() : string{
            return $this->lastName;
        }

        /**
         * @param string $lastName
         * @return void
         */
        public function setLastName(string $lastName) : void{
            $this->lastName = $lastName;
        }

        /**
         * @return array
         */
        public function getRoles() : array{
            return $this->roles;
        }

        /**
         * @param array $roles
         * @return void
         */
        public function setRoles(array $roles) : void{
            $this->roles = $roles;
        }

        /**
         * @param string $role
         * @return boolean
         */
        public function hasRole(string $role) : bool{
            return in_array($role, $this->roles);
        }

        /**
         * @param array $roles
         * @return boolean
         */
        public function hasAnyOfRoles(array $roles) : bool{
            foreach($roles as $role){
                $result = $this->hasRole($role);
                if($result){
                    return true;
                }
            }
            return false;
        }

        /**
         * @param \stdClass $array
         * @return WpUser
         */
        public static function map(\stdClass $array) : WpUser{
            $user = new WpUser();

            $user->setId($array->ID);                      
            $user->setLogin($array->user_login);
            $user->setEmail($array->user_email);
            $user->setNickName($array->user_nicename);
            $user->setFirstName($array->firstName);
            $user->setLastName($array->lastName);
            $user->setRoles($array->roles);

            return $user;
        }
    }
?>