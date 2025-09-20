<?php
    class LTMP{
        public $parse_error = false;
        public function parse($text){
            $textoff = 0;
            $tokenbuff = [];
            $output = "";
            $keyword = "";
            $last_keywords = [];
            $last_keywords_off = 0;
            $block_start = false;
            $block_end = false;
            //Lexer
            while($textoff < strlen($text)){
                if($text[$textoff] == '['){
                    $tokenbuff[$textoff] = 1;
                }elseif($text[$textoff] == ']'){
                    $tokenbuff[$textoff] = 2;
                }elseif(ctype_space($text[$textoff])){
                    $tokenbuff[$textoff] = 3;
                }else{
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
                        $keyword .= $text[$textoff++];
                    }
                    switch($keyword){
                        case "BOLD":
                            $output .= "<b>";
                            break;
                        case "ITALIC":
                            $output .= "<i>";
                            break;
                        case "CODE":
                            $output .= "<pre>";
                            break;
                        case "CODEIN":
                            $output .= "<code>";
                            break;
                        default:
                            $this->parse_error = true;
                            return;
                    }
                    $textoff++;
                    $block_start = false;
                    $last_keywords[$last_keywords_off++] = $keyword;
                    continue;
                }
                if($block_end){
                    $last_keywords_off--;
                    switch($last_keywords[$last_keywords_off]){
                        case "BOLD":
                            $output .= "</b>";
                            break;
                        case "ITALIC":
                            $output .= "</i>";
                            break;
                        case "CODE":
                            $output .= "</pre>";
                            break;
                        case "CODEIN":
                            $output .= "</code>";
                            break;
                    }
                    $textoff++;
                    $block_end = false;
                    continue;
                }
                switch($tokenbuff[$textoff]){
                    case 0:
                        $output .= $text[$textoff++];
                        continue 2;
                    case 1:
                        //block start here
                        $block_start = true;
                        $textoff++;
                        continue 2;
                    case 2:
                        //block end here
                        $block_end = true;
                        continue 2;
                    case 3:
                        $output .= $text[$textoff++];
                        continue 2;
                }
            }
            return $output;
        }
    }
    $ltmp = new LTMP();
    $result = $ltmp->parse("This is a [BOLD bold] text.<br>This is an [ITALIC italic] text.<br>[ITALIC [BOLD This]] is a bold and italic text.<br>[CODE this is a code.]");
    if($ltmp->parse_error){
      die("Failed to parse!");
    }
    echo $result;
?>