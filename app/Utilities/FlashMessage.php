<?php

    namespace WP\Utilities;

    use Nette\Http\Session;
    use Nette\Http\RequestFactory;
    use Nette\Http\Response;

    class FlashMessage{

        private $session;
        private $sessionSection;

        public function __construct(){
            $requestFatory = new RequestFactory();
            $request = $requestFatory->createHttpRequest();
            $response = new Response();

            $this->session = new Session($request, $response);
            $this->sessionSection = $this->session->getSection('flash-message');
        }

        /**
         * @param string $type
         * @param string $message
         * 
         * @return void
         */
        public function addMessage(string $type, string $message) : void{
            $this->sessionSection->messages[] = ['type' => $type, 'message' => $message];
        }    
    
        /**
         * @return array
         */
        public function getMessage() : ?array{
            $messages = $this->sessionSection->messages;
            $this->cleanSession();
            return $messages;
        }

        /**
         * @return void
         */
        private function cleanSession() : void{
            unset($this->sessionSection->messages);
        }
    }
?>