<?php

namespace WP\Extension\Latte;

use \Latte\Macros\MacroSet;
use \Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;

class Macro extends MacroSet{

    /**
     * @param Compiler $compiler
     * @return string
     */
    public static function install(Compiler $compiler){
        $set = new static($compiler);
        
        $set->addMacro('wpLink', function ($node, $writer) {

            $str = $node->tokenizer->joinAll();
            $params = explode(',', $str);

            $resourceFunc = $params[0];
            unset($params[0]);
            $resourceFuncArray = explode(':', trim($resourceFunc));

            $args = [];
            foreach($params as $param){
                $paramExplode = explode('=>', $param);
                $args[trim($paramExplode[0])] = trim($paramExplode[1]); 
            }

            $link = \WP\Utilities\WpLinkGenerator::getLink(\get_site_url(), $resourceFuncArray[0], $resourceFuncArray[1], $args);

            return $writer->write('echo("' . $link . '");');
        });

        $set->addMacro('wpPermalink', function($node, $writer){
            return $writer->write('echo get_permalink(%node.word);');
        });

        $set->addMacro('shortcode', function ($node, $writer) {
            return $writer->write('echo do_shortcode(%node.word)');
        });

        $set->addMacro('translation', function($node, $writer){
            $type = $node->tokenizer->fetchWord();
            $value = $node->tokenizer->fetchWord();

            $string = "echo \WP\Utilities\FrontendTranslation::getTranslation('". $type ."',". $value .");";

            return $writer->write($string);
        });

        $set->addMacro('wpImage', function($node, $writer){
            $image = $node->tokenizer->fetchWord();
            $size = $node->tokenizer->fetchWord();

            $string =  "if(". $image ."){";
            $string .= "    if(!empty(". $image ."->sizes)){";
            $string .= "        if(isset(". $image ."->sizes['". $size ."'])){";
            $string .= "            echo " . $image . "->sizes['". $size ."']['file'];";
            $string .= "        }else{";
            $string .= "            echo " . $image . "->sizes['full']['file'];";
            $string .= "        }";
            $string .= "    }else{";
            $string .= "        echo " . $image . "->url;";
            $string .= "    }";
            $string .= "}";
            
            return $writer->write($string);
        });
    }
}
?>