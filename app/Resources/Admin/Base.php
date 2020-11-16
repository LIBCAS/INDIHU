<?php
    namespace WP\Admin\Resources;

    class Base{

        /**
         * @param string $templateName
         * @param array $param
         * @return string
         */
        protected function renderShortcode(string $templateName, array $param) : string{
            ob_start();
            render($templateName, $param);
            $shortcode = ob_get_clean();
            return $shortcode;
        }

        /**
         * @param string $filename
         * @param string $data
         * @return void
         */
        protected function sendCsv(string $filename, string $data) : void{
            
            header("content-type:application/csv;charset=UTF-8");
            // force download  
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");

            echo $data; exit;
        }
    }
?>