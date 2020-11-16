<?php

namespace WP\Extension\Latte\Filters;

class ActiveContent{

    public function __invoke($content){
        return apply_filters('the_content', $content);
    }

}
?>