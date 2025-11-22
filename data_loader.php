<?php
header('Content-Type: application/json');

include 'data_wholesale_fmcg.php';
include 'data_wholesale_fresh_staple.php';
include 'data_progressive_pricing_fmcg.php';
include 'data_pass_moving_items.php';
include 'data_progressive_pricing_fresh_staple.php';

$sheet = isset($_GET['sheet']) ? $_GET['sheet'] : 'wholesale_fmcg';

$response = [];

function addImageColumn($data) {
    return array_map(function($row) {
        $description = strtoupper($row[0]); // Assume first column is description
        // Generate image URL using Unsplash for real images based on description
        if (stripos($description, 'KERATIN SHMPO') !== false) $query = 'shampoo';
        elseif (stripos($description, 'SARDINES') !== false) $query = 'sardines';
        elseif (stripos($description, 'BLEACH') !== false || stripos($description, 'ZONROX') !== false) $query = 'bleach';
        elseif (stripos($description, 'BEAR B') !== false || stripos($description, 'MILK') !== false) $query = 'milk';
        elseif (stripos($description, 'NESCAFE') !== false) $query = 'coffee';
        elseif (stripos($description, 'CHUCKIE') !== false) $query = 'juice';
        elseif (stripos($description, 'BEAR BRAND') !== false) $query = 'powdered+milk';
        elseif (stripos($description, 'NESCAFE 3N1') !== false) $query = 'instant+coffee';
        elseif (stripos($description, 'CHAMPION BAR') !== false) $query = 'soap';
        elseif (stripos($description, 'DATU PUTI') !== false) $query = 'condiments';
        elseif (stripos($description, 'PAPA BANANA') !== false) $query = 'ketchup';
        elseif (stripos($description, 'MAGIC FLKS') !== false) $query = 'cereal';
        elseif (stripos($description, 'PIATTOS') !== false) $query = 'chips';
        elseif (stripos($description, 'COW BELL') !== false) $query = 'condensed+milk';
        elseif (stripos($description, 'TUNA') !== false) $query = 'tuna';
        elseif (stripos($description, 'WOW ULAM') !== false) $query = 'canned+food';
        elseif (stripos($description, 'EMPERADOR') !== false || stripos($description, 'TANDUAY') !== false) $query = 'liquor';
        elseif (stripos($description, 'SILVER SWAN') !== false) $query = 'soy+sauce';
        elseif (stripos($description, 'FIESTA BEEF LOAF') !== false) $query = 'meatloaf';
        elseif (stripos($description, 'LION TIGER') !== false) $query = 'cigarettes';
        elseif (stripos($description, 'GREAT TASTE') !== false) $query = 'candy';
        elseif (stripos($description, 'OISHI') !== false) $query = 'snacks';
        elseif (stripos($description, 'RINBEE') !== false) $query = 'cheese+sticks';
        elseif (stripos($description, 'YNGS TWN') !== false) $query = 'sardines';
        elseif (stripos($description, 'NATURES SPRING') !== false) $query = 'water+bottle';
        elseif (stripos($description, 'SANICARE') !== false) $query = 'baby+wipes';
        elseif (stripos($description, 'REBISCO') !== false) $query = 'biscuits';
        elseif (stripos($description, 'FUNKY') !== false) $query = 'sauce';
        elseif (stripos($description, 'FAMILYS SRDNS') !== false) $query = 'sardines';
        elseif (stripos($description, 'FIGHTER') !== false) $query = 'wine';
        elseif (stripos($description, 'WL') !== false) $query = 'imported+snacks';
        elseif (stripos($description, 'LEMON SQ') !== false) $query = 'soft+drink';
        elseif (stripos($description, 'TANG') !== false) $query = 'juice+powder';
        elseif (stripos($description, 'BLUE BIRD') !== false) $query = 'mallorca';
        elseif (stripos($description, 'SUPER STX') !== false) $query = 'chocolate+chips';
        elseif (stripos($description, 'KOPIKO BLANCA') !== false) $query = 'chocolate+candy';
        elseif (stripos($description, 'WAFELLO') !== false) $query = 'wafer';
        elseif (stripos($description, 'ROYAL TRU') !== false) $query = 'orange+juice';
        elseif (stripos($description, 'DWNYGARDENBLOOM69') !== false) $query = 'fabric+softener';
        elseif (stripos($description, 'TIDE LAU BAR') !== false) $query = 'laundry+soap';
        elseif (stripos($description, 'ARIEL DTX') !== false) $query = 'detergent';
        elseif (stripos($description, 'EFFICASCENT') !== false) $query = 'cleaning+product';
        elseif (stripos($description, 'NURSY BABY') !== false) $query = 'baby+wipes';
        elseif (stripos($description, 'JAMAICA LIME') !== false) $query = 'juice';
        elseif (stripos($description, 'TANDUAY 5 YEARS') !== false) $query = 'liquor';
        elseif (stripos($description, 'PLUS KING') !== false) $query = 'juice';
        elseif (stripos($description, 'QUICK CHOW') !== false) $query = 'noodles';
        elseif (stripos($description, 'MARどんな KO SRDNS') !== false) $query = 'sardines';
        elseif (stripos($description, 'SUKI BIHON') !== false) $query = 'noodles';
        elseif (stripos($description, 'PRIDE WFBCND') !== false) $query = 'detergent';
        elseif (stripos($description, 'SMART') !== false) $query = 'washing+powder';
        elseif (stripos($description, 'STAR WAX') !== false) $query = 'wax';
        elseif (stripos($description, 'KENDI MINT') !== false) $query = 'candy';
        elseif (stripos($description, 'WHITE RABBIT') !== false) $query = 'candy';
        elseif (stripos($description, 'SFG BAR') !== false) $query = 'soap';
        elseif (stripos($description, 'FISH FANTASEA') !== false) $query = 'fish+snacks';
        elseif (stripos($description, 'DM MIXD FRT') !== false) $query = 'jelly';
        elseif (stripos($description, 'LUCKY ME') !== false) $query = 'noodles';
        elseif (stripos($description, 'MY SN') !== false) $query = 'chocolate';
        elseif (stripos($description, 'MY SN GRHM') !== false) $query = 'graham+crackers';
        elseif (stripos($description, 'NISSIN') !== false) $query = 'ramen';
        elseif (stripos($description, 'RICH EE') !== false) $query = 'candy+stick';
        elseif (stripos($description, 'VINO KULAFU') !== false) $query = 'yogurt+drink';
        elseif (stripos($description, 'SILKA') !== false) $query = 'soap';
        elseif (stripos($description, 'LUCKY ME SP') !== false) $query = 'spicy+noodles';
        elseif (stripos($description, 'SURF') !== false) $query = 'laundry+powder';
        elseif (stripos($description, 'ARIEL') !== false) $query = 'detergent+powder';
        elseif (stripos($description, 'LAMP EIN') !== false) $query = 'toilet+paper';
        elseif (stripos($description, 'SISTERS') !== false) $query = 'napkins';
        elseif (stripos($description, 'SUPER Q') !== false) $query = 'noodles';
        // Add more as needed
        else $query = 'consumer+product';
        $img = 'https://source.unsplash.com/50x50/?' . $query;
        return array_merge(['<img src="' . $img . '" width="50" height="50" alt="' . htmlspecialchars(substr($description, 0, 10)) . '" loading="lazy">'], $row);
    }, $data);
}

switch($sheet) {
    case 'wholesale_fmcg':
        $response['headers'] = $wholesale_fmcg_headers;
        $response['data'] = addImageColumn($wholesale_fmcg_data);
        break;
    case 'wholesale_fresh_staple':
        $response['headers'] = $wholesale_fresh_staple_headers;
        $response['data'] = addImageColumn($wholesale_fresh_staple_data);
        break;
    case 'progressive_pricing_fmcg':
        $response['headers'] = $progressive_pricing_fmcg_headers;
        $response['data'] = addImageColumn($progressive_pricing_fmcg_data);
        break;
    case 'pass_moving_items':
        $response['headers'] = $pass_moving_items_headers;
        $response['data'] = addImageColumn($pass_moving_items_data);
        break;
    case 'progressive_pricing_fresh_staple':
        $response['headers'] = $progressive_pricing_fresh_staple_headers;
        $response['data'] = addImageColumn($progressive_pricing_fresh_staple_data);
        break;
}

// Add Image header
array_unshift($response['headers'], 'Image');

$encoded = base64_encode(json_encode($response));
echo $encoded;
?>
