<html>
<head>
    <title>Huffman algorithm</title>
    <style>
        
        div.kids, div.kid {  
            border: 1px solid transparent; 
            margin: 6px; 
            vertical-align: middle; 
        }
        div.main:hover * {
            border-color: transparent;
        }
        div.kids {
            border-color: #BBBBBB transparent transparent transparent;
        }
        div.kids:hover {
            border-color: black transparent transparent transparent;
        }
        div.encoded {
            display: inline-block;
            height: 22px;
            vertical-align: middle;
        }
        div.path {
            text-align: center;
        }
        div.encoded, div.path span {
            display: block;
            visibility: hidden;
            height: 22px;
            padding: 0 4px;
            vertical-align: middle;
            text-align: center;
        }
        div.kid {
            display: inline-block;
            vertical-align: top;
            text-align: center;
            
        }
        div.main:hover div.kid:first-child {
            border-color: transparent;
        }
        div.main div.kid:first-child:hover, div.kid:first-child:hover {
            border-color: transparent black transparent transparent;
        }
        div.kid:last-child:hover {
            border-color: transparent transparent transparent black;
        }
        div.kid:first-child {
            border-color: transparent #BBBBBB transparent transparent;
        }
        div.kid:last-child {
            border-color: transparent;
        }
        div.kid:hover {
            background-color: rgba(255,150,150,0.3);
        }
        div.kid:hover > div > div > span {
            visibility: visible;
            background-color: rgba(150,150,255,0.8);
        }
        div.kid:hover > div > div.encoded {
            visibility: visible;
            background-color: rgba(150,255,150,0.8);
        }
        pre {display: inline;}
    </style>
</head>
<body>
<?php 
$string = 'Welcome to Koding Beta';
if (isset($_GET['tryme'])) {
    $string = $_GET['tryme'];
}
?>
<h2>Input:</h2>
<div>
<pre><?php echo $string; ?></pre>
</div>
<?php


$weights = array();
$cursor = -1;
while ($symbol=$string[++$cursor]) {
    $weights[$symbol] = isset($weights[$symbol])?$weights[$symbol]:array('path'=>$symbol, 'value'=>0);
    $weights[$symbol]['value']++;
}


function combineNodes($weights) {
    if (count($weights)==1) {
        return $weights;
    }
    
    $left = $right = null;
    $key1 = $key2 = null;
    foreach($weights as $symbol => $weight) {
        if (is_null($left)) {
            $left=$weight;
            $key1=$symbol;
        } elseif ($left['value']>$weight['value']) {
            $left=$weight;
            $key1=$symbol;
        }
    }
    
    $weights[$key1]=array('value'=>'__placeholder__');

    foreach($weights as $symbol => $weight) {
        if ($weight['value']=='__placeholder__') {
            continue;
        }
        if (is_null($right)) {
            $right=$weight;
            $key2=$symbol;
        } elseif ($right['value']>$weight['value']) {
            $right=$weight;
            $key2=$symbol;
        }
    }
    
    unset($weights[$key2]);
    
    $weights[$key1] = array(
        'path'=>$left['path'].$right['path'],
        'value'=>$left['value']+$right['value'],
        'left'=>$left,
        'right'=>$right
    );
    return combineNodes($weights);
}

function displayTree($weights, $dir=null, $encoded='') {
    global $encodings;
    if (!isset($weights['path'])) {
        $weights = array_values($weights);
        $weights = $weights[0];
    }
    
    $encoded = $dir.$encoded;
    
    $hasKids = isset($weights['left']);

    $output = '<div>';
    $output .= '<div class="path'.($hasKids?'':' last').'" title="'.$weights['value'].'">'.
    (!is_null($dir)?'<span>'.$dir.'</span>':'').
    
    ' <pre>'.$weights['path'].'</pre><sub>'.$weights['value'].'</sub>'."</div>";
    if ($hasKids) {
        $output .= '<div class="kids">';
        $output .= '<div class="kid">'.displayTree($weights['left'], 0, $encoded).'</div>';
        $output .= '<div class="kid">'.displayTree($weights['right'], 1, $encoded).'</div>';
        $output .= '</div>';
    } else {
        $encodings[$weights['path']]=array('weight'=>$weights['value'], 'enc'=>$encoded);
        $output .= '<div class="encoded">'.$encoded.'</div>';
    }
    $output .= '</div>';
    return $output;
}

$weights = combineNodes($weights);

$encodings = array();
echo "<h2>Tree:</h2>";
echo '<div class="main">';
echo displayTree($weights);
echo '</div>';
arsort($encodings);
echo "<h2>Map:</h2>";
echo "<div>";
foreach($encodings as $symbol=>$encoded) {
    echo "<pre>$symbol</pre> - <b>".$encoded['weight']."</b> - ".$encoded['enc']."<br />";    
}
echo "</div>";
echo "<h2>Encoded:</h2>";
echo "<div>";

$strlen = strlen($string);

for($i=0; $i<$strlen; $i++) {
    $symbol=$string[$i];
    echo $encodings[$symbol]['enc'];    
}
echo "</div>";
?>
</body>
</html>