<?php



if(isset($_REQUEST['save_data'])){
    $label = strtoupper($_REQUEST['label']);
    $pixel = json_decode($_REQUEST['pixels']);

    $data = [$label];
    foreach ($pixel as $p){
        $data[] = $p;
    }
    // array_push($data, $pixel);
    $output = fopen("./datasets/d4.csv",'a') or die("Can't open php://output");
    fputcsv($output, $data);
}

if(isset($_REQUEST['save_data_array'])){
    $label1 = [strtoupper($_REQUEST['value1'])];
    $label2 = [strtoupper($_REQUEST['value2'])];
    $label3 = [strtoupper($_REQUEST['value3'])];
    $label4 = [strtoupper($_REQUEST['value4'])];
    $label5 = [strtoupper($_REQUEST['value5'])];
    $label6 = [strtoupper($_REQUEST['value6'])];

    $pixel1 = json_decode($_REQUEST['pixels1']);
    $pixel2 = json_decode($_REQUEST['pixels2']);
    $pixel3 = json_decode($_REQUEST['pixels3']);
    $pixel4 = json_decode($_REQUEST['pixels4']);
    $pixel5 = json_decode($_REQUEST['pixels5']);
    $pixel6 = json_decode($_REQUEST['pixels6']);

    $pixel1 = array_merge($label1, $pixel1);
    $pixel2 = array_merge($label2, $pixel2);
    $pixel3 = array_merge($label3, $pixel3);
    $pixel4 = array_merge($label4, $pixel4);
    $pixel5 = array_merge($label5, $pixel5);
    $pixel6 = array_merge($label6, $pixel6);

    // array_push($data, $pixel);
    $output = fopen("./datasets/d4.csv",'a') or die("Can't open php://output");
    fputcsv($output, $pixel1);
    fputcsv($output, $pixel2);
    fputcsv($output, $pixel3);
    fputcsv($output, $pixel4);
    fputcsv($output, $pixel5);
    fputcsv($output, $pixel6);
}


if( isset($_REQUEST['predict']) ){
    $fname = date('U').'_'.rand(1111,9999);
    $output = fopen("./predict/".$fname.".csv",'a') or die("Can't open php://output");

    fputs($output, str_replace(['[', ']'], ['',''], $_REQUEST['pixels1']) . "\n" );
    fputs($output, str_replace(['[', ']'], ['',''], $_REQUEST['pixels2']) . "\n" );
    fputs($output, str_replace(['[', ']'], ['',''], $_REQUEST['pixels3']) . "\n" );
    fputs($output, str_replace(['[', ']'], ['',''], $_REQUEST['pixels4']) . "\n" );
    fputs($output, str_replace(['[', ']'], ['',''], $_REQUEST['pixels5']) . "\n" );
    fputs($output, str_replace(['[', ']'], ['',''], $_REQUEST['pixels6']) . "\n" );

//    fputcsv($output, $_REQUEST['pixels1']);
//    fputcsv($output, $_REQUEST['pixels2']);
//    fputcsv($output, $_REQUEST['pixels3']);
//    fputcsv($output, $_REQUEST['pixels4']);
//    fputcsv($output, $_REQUEST['pixels5']);
//    fputcsv($output, $_REQUEST['pixels6']);

    echo exec('python predict.py '.$fname);
}

if( isset($_REQUEST['bulk_predict']) ){
    $fname = $_REQUEST['fname'];
    $out = array();
    exec('python predict.py '.$fname, $out);
    foreach( $out as $line) { echo $line; }
}

?>