<?php

class search_token_parse{
    public $tokens,
        $statements,
        $structure,
        $mode = 'query',
        $quotes_pattern = <<<'HERE'
~(?>^|[^\\])\"(.+[^\\])\"~U
HERE,
        $column_pattern = '/([a-zA-Z0-9_]+):([^,\r\n\t\f\v ]+)/',
        $negate_pattern = '/\-([^,\r\n\t\f\v ]+)/',
        $and_pattern = '/\+([^,\r\n\t\f\v ]+)/';

    public function token_replace($string){
        $quotes = [];
        preg_match_all($this->quotes_pattern, $string, $quotes);
        if(!empty($quotes)){
            foreach($quotes[0] as $key => $full_match){
                if(substr($full_match, 0, 1) != '"'){
                    $full_match = substr($full_match, 1);
                }
                $string = str_replace($full_match, '{quote|'.$key.'}', $string);
                $this->tokens['{quote|'.$key.'}'] = [
                    'type' => 'quote',
                    'value' => $quotes[1][$key]
                ];
            }
        }
        $columns = [];
        preg_match_all($this->column_pattern, $string, $columns);
        if(!empty($columns)){
            foreach($columns[0] as $key => $full_match){
                $string = str_replace($full_match, '{column|'.$key.'}', $string);
                $this->tokens['{column|'.$key.'}'] = [
                    'type' => 'column',
                    'column' => $columns[1][$key],
                    'value' => $columns[2][$key]
                ];
            }
        }
        $negates = [];
        preg_match_all($this->negate_pattern, $string, $negates);
        if(!empty($negates)){
            foreach($negates[0] as $key => $full_match){
                $string = str_replace($full_match, '{negate|'.$key.'}', $string);
                $this->tokens['{negate|'.$key.'}'] = [
                    'type' => 'negate',
                    'value' => $negates[1][$key]
                ];
            }
        }
        $ands = [];
        preg_match_all($this->and_pattern, $string, $ands);
        if(!empty($ands)){
            foreach($ands[0] as $key => $full_match){
                $string = str_replace($full_match, '{and|'.$key.'}', $string);
                $this->tokens['{and|'.$key.'}'] = [
                    'type' => 'and',
                    'value' => $ands[1][$key]
                ];
            }
        }
        return $this->or_split($string);
    }

    public function or_split($string){
        $array = explode(',', $string);
        foreach($array as $key => $value){
            $array[$key] = explode(' ', trim($value));
        }
        return $array;
    }

    public function parse_tokens($token_array){
        foreach($token_array as $s_key => $statement){
            foreach($statement as $key => $item){
                    $statement[$key] = $this->recursive_token($item);
            }
            $token_array[$s_key] = $statement;
        }
        return $token_array;
    }

    public function recursive_token($item){
        if(!empty($this->tokens[$item])){
            $item = $this->tokens[$item];
            if($item['type'] != 'quote'){
                $item['value'] = $this->recursive_token($item['value']);
            }else{
                $item = $item['value'];
            }
        }
        return $item;
    }

    public function parse($string){
        $token_array = $this->token_replace($string);
        $search_array = $this->parse_tokens($token_array);
        return $search_array;
    }

    public function token_array_to_sub_queries($token_array, $default_columns, $all_columns){
        $query = [
            'query_mode' => 'OR',
            'sub_query' => []
        ];
        foreach($token_array as $sub_query){
            $query['sub_query'][] = $this->recursive_sub_queries($sub_query, $default_columns, $all_columns);
        }
        return $query;
    }

    public function recursive_sub_queries($array, $default_columns, $all_columns){
        $mode = 'AND';
        $sub_query = [];
        if(is_array($array) && !empty($array['type'])){
            switch($array['type']){
                case 'column':
                    if(in_array($array['column'], $all_columns)){
                        $sub_query['search_'.$array['column']] = '%'.$array['value'].'%';
                    }
                    break;
                case 'negate':
                    if(is_string($array['value'])){
                        foreach($default_columns as $default){  
                            $sub_query['not_search_'.$default] = '%'.$array['value'].'%';
                        }
                    }else{
                        if(empty($sub_query['sub_query'])){
                            $sub_query['sub_query'] = [];
                        }
                        $sub_query['sub_query'][] = $this->recursive_sub_queries($array['value'], $default_columns, $all_columns);
                    }
                    break;
            }
        }else if(is_array($array)){
            $temp_array = [];
            $type = 'string';
            foreach($array as $item){
                if(is_string($item)){
                    $safe_item = $item;
                    $temp_array[] = $safe_item;
                }else{
                    $type = 'complex';
                    $temp_result = $this->recursive_sub_queries($item, $default_columns, $all_columns);
                    if(empty($sub_query['sub_query'])){
                        $sub_query['sub_query'] = [];
                    }
                    $sub_query['sub_query'][] = $temp_result;
                }
            }
            if($type == 'string'){
                $temp_array = implode('%', $temp_array);
                foreach($default_columns as $default){                 
                    $sub_query['search_'.$default] = '%'.$temp_array.'%';
                }
                $mode = 'OR';
            }
        }else if(is_string($array)){
            foreach($default_columns as $column){
                $sub_query['search_'.$column] = '%'.$array.'%';
            }
        }
        if(count($sub_query) > 1){
            $sub_query['query_mode'] = $mode;
        }else{
            if(!empty($sub_query['sub_query'])){
                if(count($sub_query['sub_query']) > 0){
                    return $sub_query['sub_query'];
                }else{
                    return $sub_query['sub_query'][0];
                }
            }
        }
        foreach($sub_query as $key => $value){
            if(empty($value)){
                unset($item[$key]);
            }
        }
        if(count($sub_query) == 1 && !empty($sub_query[0])){
            return $sub_query[0];
        }
        return $sub_query;
    }
}