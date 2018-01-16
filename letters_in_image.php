<?php

$files = glob('./captcha/*.jpg');
$cracked = 0;
echo "<table border='1' cellspacing='0' cellpadding='5'>";
$tabindex = 0;
$bigI = ["x" => 0, "y" => 0];
$start_from = $_GET['index'] or die('');
$fname = date('U') . '_' . rand(1111, 9999);

for ($f = $start_from; $f < $start_from + 30; $f++) {

    $fim = imagecreatefromjpeg($files[$f]);
    $fx = imagesx($fim);
    $fy = imagesy($fim);
    $im = imagecreatetruecolor(180, 45);
    imagecopyresampled($im, $fim, 0, 0, 20, 0, 180, 45, 180, 45);

    imagejpeg($im, 'captcha.jpg');

    imagefilter($im, IMG_FILTER_NEGATE);
    $data = [];
    $pixels = [];
// echo "Dimensions: ". imagesx($im) . ' x '.imagesy($im).'px'."\n";

    $ft = 225;
    for ($i = 0; $i < imagesy($im); $i++) {
        for ($j = 0; $j < imagesx($im); $j++) {
            $c = rgb(imagecolorat($im, $j, $i));
            $pixels[$i][$j] = $c;
            if ($j == 0) {
                echo "\n";
                $data[$i] = array();
            }
            if ($c[0] > $ft && $c[1] > $ft && $c[2] > $ft) {
                $data[$i][$j] = 0; // black point
                // echo '+';
            } else {
                $data[$i][$j] = 1; // white point
                // echo ' ';
            }
        }
    }

// echo "\n\n";

    $letters_data = [[]];
    $ldi = 0;
    $crop_x = [];
    $xcols = 0;
    for ($i = 0, $xcols = 0; $i < imagesx($im); $i++, $xcols++) {
        for ($j = 0, $points = 0; $j < imagesy($im); $j++) {
            array_push($letters_data[$ldi], $data[$j][$i]);
            if ($data[$j][$i] == 0) $points++;
        }
        if ($points < 2) {
            if ($xcols <= 5 && $xcols >= 3) {
                continue;
            }
            $crop_x[] = $i;
            $ldi++;
            $xcols = 0;
            $letters_data[$ldi] = [];
            $i += 10;
        }
    }

    // Pad Letters Data
    if (count($crop_x) < 7) {
        continue;
    }

    imagefilter($im, IMG_FILTER_NEGATE);
    ob_start();
    imagejpeg($im);
    $imgdata = ob_get_contents();
    ob_end_clean();
    echo "<tr>";
    echo '<td>' . count($crop_x) . '<img src="data:image/jpeg;base64,' . base64_encode($imgdata) . '" /></td>';
    imagefilter($im, IMG_FILTER_NEGATE);
    // print_r($crop_x);


    $handle = fopen('./predict/' . $fname . '.csv', 'a');

    for ($i = 0; $i < count($crop_x) - 1 && $i < 6; $i++) {
        // echo 'CROPPING FROM '. $crop_x[$i] .' TO '.$crop_x[$i+1]. "\n";
        $w = $crop_x[$i + 1] - $crop_x[$i];
        $letter = imagecreatetruecolor($w, imagesy($im));

        imagecopyresampled($letter, $im, 0, 0, $crop_x[$i], 0, $w, imagesy($im), $w, imagesy($im));
        imagejpeg($letter, './steps/ltr-' . $i . '.jpg');
        imagefilter($letter, IMG_FILTER_NEGATE);
        $white = imagecolorallocate($im, 255, 255, 255);

        if ($i == 0) $letter = imagerotate($letter, 10, $white);
        elseif ($i == 1) $letter = imagerotate($letter, -20, $white);
        elseif ($i == 2) $letter = imagerotate($letter, 20, $white);
        elseif ($i == 3) $letter = imagerotate($letter, -20, $white);
        elseif ($i == 4) $letter = imagerotate($letter, 10, $white);
        elseif ($i == 5) $letter = imagerotate($letter, -10, $white);

        $letter = remove_white_padding($letter);

        ob_start();
        imagejpeg($letter);
        $imgdata = ob_get_contents();
        ob_end_clean();

        $pixel_data = get_pixels($letter);
        if ($bigI['x'] < imagesx($letter)) $bigI['x'] = imagesx($letter);
        if ($bigI['y'] < imagesy($letter)) $bigI['y'] = imagesy($letter);

        fputcsv($handle, $pixel_data);
        echo '<td><img src="data:image/jpeg;base64,' . base64_encode($imgdata) . '" /><br><input data-pixels="' . json_encode($pixel_data) . '" placeholder="' . imagesx($letter) . 'x' . imagesy($letter) . '" type="text" style="width:40px" tabindex="' . ($tabindex++) . '" /></td>';

//        if($i != 5) {
//            echo '<td><img src="data:image/jpeg;base64,'. base64_encode($imgdata) .'" /><br><input data-pixels="'.json_encode($pixel_data).'" placeholder="'. count($pixel_data) .'" type="text" style="width:40px" tabindex="'.($tabindex++).'" /></td>';
//        }else{
//            echo '<td><img src="data:image/jpeg;base64,'. base64_encode($imgdata) .'" /><br><input data-pixels="'.json_encode($pixel_data).'" placeholder="'. count($pixel_data) .'" type="text" style="width:40px" tabindex="'.($tabindex++).'" data-submit="true" /></td>';
//        }

        imagedestroy($letter);
    }

    if (count($crop_x) - 1 == 5) echo "<td></td>";

    if (count($crop_x) - 1 > 5) {
        echo "<td><button class='predict' tabindex='" . ($tabindex++) . "'>Predict</button><br/><button class='save' tabindex='" . ($tabindex++) . "'>Save</button></td>";
        $cracked++;
    } else echo "<td></td>";

    if ($f % 2 == 0) echo "</tr><tr>";
}

echo "</tr></table>";

echo "<h1>Cracked: $cracked / " . ($f - $start_from) . " ";
echo "<a href='letters_in_image.php?index=$f' tabindex='$tabindex'>NEXT</a></h2>";
echo $bigI['x'] . "x" . $bigI['y'];

function get_pixels($im)
{
    $data = [];
    $ft = 225;
    for ($i = 0; $i < imagesy($im); $i++) {
        for ($j = 0; $j < imagesx($im); $j++) {
            $c = rgb(imagecolorat($im, $j, $i));
            if ($c[0] > $ft && $c[1] > $ft && $c[2] > $ft) {
                array_push($data, 0);
            } else {
                array_push($data, 1);
            }
        }
    }
    return $data;
}

function remove_white_padding($img)
{
    $b_top = 0;
    $b_btm = 0;
    $b_lft = 0;
    $b_rt = 0;

    //top
    for (; $b_top < imagesy($img); ++$b_top) {
        for ($x = 0; $x < imagesx($img); ++$x) {
            $c = rgb(imagecolorat($img, $x, $b_top));
            if ($c[0] < 225 && $c[1] < 225 && $c[2] < 225) {
                break 2; //out of the 'top' loop
            }
        }
    }

    //bottom
    for (; $b_btm < imagesy($img); ++$b_btm) {
        for ($x = 0; $x < imagesx($img); ++$x) {
            $c = rgb(imagecolorat($img, $x, imagesy($img) - $b_btm - 1));
            if ($c[0] < 225 && $c[1] < 225 && $c[2] < 225) {
                break 2; //out of the 'bottom' loop
            }
        }
    }

    //left
    for (; $b_lft < imagesx($img); ++$b_lft) {
        for ($y = 0; $y < imagesy($img); ++$y) {
            $c = rgb(imagecolorat($img, $b_lft, $y));
            if ($c[0] < 225 && $c[1] < 225 && $c[2] < 225) {
                break 2; //out of the 'left' loop
            }
        }
    }

    //right
    for (; $b_rt < imagesx($img); ++$b_rt) {
        for ($y = 0; $y < imagesy($img); ++$y) {
            $c = rgb(imagecolorat($img, imagesx($img) - $b_rt - 1, $y));
            if ($c[0] < 225 && $c[1] < 225 && $c[2] < 225) {
                break 2; //out of the 'right' loop
            }
        }
    }

    // $newimg = imagecreatetruecolor(imagesx($img)-($b_lft+$b_rt), imagesy($img)-($b_top+$b_btm));
    $newimg = imagecreatetruecolor(35, 40);
    imagecopyresized($newimg, $img, 0, 0, $b_lft, $b_top, imagesx($newimg), imagesy($newimg), imagesx($img) - ($b_lft + $b_rt), imagesy($img) - ($b_top + $b_btm));
    return ($newimg);
}

function rgb($rgb)
{
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    return [$r, $g, $b];
}

?>
<style>
    input {
        text-align: center;
    }
</style>
<script src="jquery.min.js"></script>
<script>
    var predictions = [];
    $(document).ready(function () {
        var fname = '<?=$fname?>';
        $.ajax({
            url: "save_data.php",
            type: "POST",
            async: true,
            data: { bulk_predict: '', fname: fname }
        }).done(function (res) {
            res.replace(/\w+/g, function (a) {
                predictions.push(a);
                return a;
            });
            $.each($("input"), function (i, a){ a.value = predictions[i] } );
        });
    });

    $(".predict").click(function (e) {
        btn = e.currentTarget;
        var p1 = document.querySelector('[tabindex="' + (btn.tabIndex - 6) + '"]');
        var p2 = document.querySelector('[tabindex="' + (btn.tabIndex - 5) + '"]');
        var p3 = document.querySelector('[tabindex="' + (btn.tabIndex - 4) + '"]');
        var p4 = document.querySelector('[tabindex="' + (btn.tabIndex - 3) + '"]');
        var p5 = document.querySelector('[tabindex="' + (btn.tabIndex - 2) + '"]');
        var p6 = document.querySelector('[tabindex="' + (btn.tabIndex - 1) + '"]');

        var ajaxData = {
            predict: '',
            pixels1: p1.dataset['pixels'],
            pixels2: p2.dataset['pixels'],
            pixels3: p3.dataset['pixels'],
            pixels4: p4.dataset['pixels'],
            pixels5: p5.dataset['pixels'],
            pixels6: p6.dataset['pixels']
        }

        $.ajax({
            url: "save_data.php",
            type: "POST",
            async: true,
            data: ajaxData
        }).done(function (res) {
            var d = [];
            res.replace(/\w+/g, function (a) {
                d.push(a);
                return a;
            });
            p1.value = d[0];
            p2.value = d[1];
            p3.value = d[2];
            p4.value = d[3];
            p5.value = d[4];
            p6.value = d[5];
        })
    });

    $(".save").click(function (e) {
        btn = e.currentTarget;
        var p1 = document.querySelector('[tabindex="' + (btn.tabIndex - 7) + '"]');
        var p2 = document.querySelector('[tabindex="' + (btn.tabIndex - 6) + '"]');
        var p3 = document.querySelector('[tabindex="' + (btn.tabIndex - 5) + '"]');
        var p4 = document.querySelector('[tabindex="' + (btn.tabIndex - 4) + '"]');
        var p5 = document.querySelector('[tabindex="' + (btn.tabIndex - 3) + '"]');
        var p6 = document.querySelector('[tabindex="' + (btn.tabIndex - 2) + '"]');

        var ajaxData = {
            save_data_array: '',
            pixels1: p1.dataset['pixels'],
            value1: p1.value,
            pixels2: p2.dataset['pixels'],
            value2: p2.value,
            pixels3: p3.dataset['pixels'],
            value3: p3.value,
            pixels4: p4.dataset['pixels'],
            value4: p4.value,
            pixels5: p5.dataset['pixels'],
            value5: p5.value,
            pixels6: p6.dataset['pixels'],
            value6: p6.value
        };
        $(this).parent().animate('hide');
        $.ajax({
            url: "save_data.php",
            type: "POST",
            async: true,
            data: ajaxData
        }).done(function (res) {

        })
    })
</script>
