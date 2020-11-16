<?php

    class DbQueries implements Tracy\IBarPanel
    {
        /**
         * @return string
         */
        public function getTab() : string
        {
            return '<span title="Explaining tooltip">
                        DbQueries
                    </span>';
        }

        /**
         * @return string
         */
        public function getPanel() : string
        {
            global $wpdb;
            $queries = $wpdb->queries;

            $html = '';
            $html .= '<h1>Title</h1>';
            $html .= '<div class="tracy-inner">';
            $html .= '  <table>';
            $html .= '      <thead>';
            $html .= '          <tr>';
            $html .= '              <th>#</th>';
            $html .= '              <th>Query</th>';
            $html .= '              <th>Time</th>';
            $html .= '          </tr>';
            $html .= '      </thead>';
            $html .= '      <tbody>';
            foreach($queries as $key => $query){
            $html .= '          <tr>';
            $html .= '              <th scope="row">'. $key .'</th>';
            $html .= '              <td>'. $query[0] .'</td>';
            $html .= '              <td>'. $query[1] .'</td>';
            $html .= '          </tr>';
            }
            $html .= '      </tbody>';
            $html .= '  </table>';
            $html .= '</div>';
            
            return $html;
        }
    }
?>