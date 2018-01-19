<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Method: GET, POST');
header('Access-Control-Request-Method: GET, POST');

if( ! isset($_POST['image']) ) die('Send Image');

$fname = date('U') . '_' . rand(1111, 9999);
$file = base64_decode($_POST['image']);
$fim = imagecreatefromstring($file);
//header('content-type: image/jpeg');
//imagejpeg($fim);

$fx = imagesx($fim);
$fy = imagesy($fim);
$im = imagecreatetruecolor(180, 45);
imagecopyresampled($im, $fim, 0, 0, 20, 0, 180, 45, 180, 45);

imagejpeg($im, 'captcha.jpg');

imagefilter($im, IMG_FILTER_NEGATE);
$data = [];
$pixels = [];

$ft = 225;
for ($i = 0; $i < imagesy($im); $i++) {
    for ($j = 0; $j < imagesx($im); $j++) {
        $c = rgb(imagecolorat($im, $j, $i));
        $pixels[$i][$j] = $c;
        if ($j == 0) {
            $data[$i] = array();
        }
        if ($c[0] > $ft && $c[1] > $ft && $c[2] > $ft) {
            $data[$i][$j] = 0; // black point
        } else {
            $data[$i][$j] = 1; // white point
        }
    }
}

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

if (count($crop_x) < 7) {
    die('[]');
}

$handle = fopen('./predict/' . $fname . '.csv', 'a');

for ($i = 0; $i < count($crop_x) - 1 && $i < 6; $i++) {
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

    $pixel_data = get_pixels($letter);

    fputcsv($handle, $pixel_data);
    imagedestroy($letter);
}

echo shell_exec('python /home/admin/web/c.vits.me/public_html/predict.py '.$fname.' 2>&1');

// echo shell_exec('python predict.py '.$fname);

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