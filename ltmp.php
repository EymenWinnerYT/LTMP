<?php
    class LTMP{
        public $parse_error = false;
        public function parse($text){
            $textoff = 0;
            $tokenbuff = array();
            $output = "";
            $last_keywords = array();
            $last_keywords_off = 0;
            $block_start = false;
            $block_end = false;
            $variables = array();
            $tagopens = array(
                "BOLD" => "<b>",
                "ITALIC" => "<i>",
                "INCODE" => "<code>",
                "CODE" => "<pre>",
                "HEADER1" => "<h1>",
                "HEADER2" => "<h2>",
                "HEADER3" => "<h3>",
                "HEADER4" => "<h4>",
                "HEADER5" => "<h5>",
                "HEADER6" => "<h6>",
                "EXP" => "<sup>",
                "CLT" => "<sub>",
                "LINK" => '<a href="',
                "TCOLOR" => '<span style="background-color: ',
                "LIST" => '<ul>',
                "OLIST" => '<ol>',
                "LISTI" => '<li>',
                "IMAGE" => '<img src="',
                "DEFVAR" => '',
                "DISPVAR" => '',
                "!" => ''
            );
            $tagcloses = array(
                "BOLD" => "</b>",
                "ITALIC" => "</i>",
                "INCODE" => "</code>",
                "CODE" => "</pre>",
                "HEADER1" => "</h1>",
                "HEADER2" => "</h2>",
                "HEADER3" => "</h3>",
                "HEADER4" => "</h4>",
                "HEADER5" => "</h5>",
                "HEADER6" => "</h6>",
                "EXP" => "</sup>",
                "CLT" => "</sub>",
                "LINK" => "</a>",
                "TCOLOR" => "</span>",
                "LIST" => '</ul>',
                "OLIST" => '</ol>',
                "LISTI" => '</li>',
                "IMAGE" => '',
                "DEFVAR" => '',
                "DISPVAR" => '',
                "!" => ''
            );
            //Lexer
            while($textoff < strlen($text)){
                if ($text[$textoff] === '[') {
                    $tokenbuff[$textoff] = 1;
                } elseif ($text[$textoff] === ']') {
                    $tokenbuff[$textoff] = 2;
                } elseif (ctype_space($text[$textoff])) {
                    $tokenbuff[$textoff] = 3;
                } else {
                    $tokenbuff[$textoff] = 0;
                }
                $textoff++;
            }
            //Parser
            $textoff = 0;
            while($textoff < strlen($text)){
                if($block_start){
                    //skip whitespace if there
                    while($tokenbuff[$textoff] == 3){
                        $textoff++;
                    }
                    //read keyword
                    $keyword = "";
                    while($tokenbuff[$textoff] != 3){
                        if($tokenbuff[$textoff] == 2 || $tokenbuff[$textoff] == 3){
                            $this->parse_error = true; return;
                        }
                        $keyword .= $text[$textoff++];
                    }
                    if(!array_key_exists($keyword, $tagopens)){
                        $this->parse_error = true; return;
                    }
                    $textoff++;
                    $arg1 = "";
                    $arg2 = "";
                    switch($keyword){
                        case "LINK":
                            $output .= $tagopens[$keyword];
                            //read argument
                            while($tokenbuff[$textoff] != 3){
                                $arg1 .= $text[$textoff++];
                            }
                            $output .= $arg1 . '">';
                            break;
                        case "TCOLOR":
                            $output .= $tagopens[$keyword];
                            while($tokenbuff[$textoff] != 3){
                                $arg1 .= $text[$textoff++];
                            }
                            $textoff++;
                            while($tokenbuff[$textoff] != 3){
                                $arg2 .= $text[$textoff++];
                            }
                            if(ctype_digit($arg1)){
                                $prefix = "#";
                            }else{
                                $prefix = "";
                            }
                            $output .= $prefix . $arg1 . '; color: ' . $prefix . $arg2 . ';">';
                            break;
                        case "IMAGE":
                            $output .= $tagopens[$keyword];
                            while($tokenbuff[$textoff] != 2){
                                $arg1 .= $text[$textoff++];
                            }
                            $output .= $arg1 . '">';
                            break;
                        case "DEFVAR":
                            $output .= $tagopens[$keyword];
                            while($tokenbuff[$textoff] != 3){
                                $arg1 .= $text[$textoff++];
                            }
                            $textoff++;
                            while($tokenbuff[$textoff] != 2){
                                $arg2 .= $text[$textoff++];
                            }
                            $variables[$arg1] = explode(",", $arg2);
                            break;
                        case "DISPVAR":
                            $output .= $tagopens[$keyword];
                            while($tokenbuff[$textoff] != 2){
                                $arg1 .= $text[$textoff++];
                            }
                            $arg1 = explode(".", $arg1);
                            if(array_key_exists($arg1[0], $variables)){
                                if(isset($arg1[1])){
                                    $output .= $variables[$arg1[0]][$arg1[1]];
                                }else{
                                    $output .= $variables[$arg1[0]][0];
                                }
                            }else{
                                $this->parse_error = true;
                                return;
                            }
                            break;
                        case "!":
                            while($tokenbuff[$textoff++] != 2);         //ignore it until tag end
                            break;
                        default:
                            $output .= $tagopens[$keyword];
                    }
                    $textoff++;
                    $block_start = false;
                    $last_keywords[$last_keywords_off++] = $keyword;
                    continue;
                }
                if($block_end){
                    $last_keywords_off--;
                    if(!array_key_exists($last_keywords[$last_keywords_off], $tagcloses)){
                        $this->parse_error = true;
                        return;
                    }
                    $output .= $tagcloses[$last_keywords[$last_keywords_off]];
                    $textoff++;
                    $block_end = false;
                    continue;
                }
                switch($tokenbuff[$textoff]){
                    case 0: $output .= $text[$textoff++]; continue 2;
                    case 1: $block_start = true; $textoff++; continue 2;
                    case 2: $block_end = true; continue 2;
                    case 3: $output .= $text[$textoff++]; continue 2;
                }
            }
            return $output;
        }
    }
?>