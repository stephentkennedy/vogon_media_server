<?php
$pagecount = 0;
foreach($lines as $line){
    // Extract the number
    if(preg_match("/Pages:\s*(\d+)/i", $line, $matches) === 1)
    {
        $pagecount = intval($matches[1]);
        break;
    }
}

return [
    'pages' => $pagecount
];