<?php
// 画像の左右反転
function image_flop($image)
{
    // 画像の幅を取得
    $w = imagesx($image);
    // 画像の高さを取得
    $h = imagesy($image);
    // 変換後の画像の生成（元の画像と同じサイズ）
    $destImage = @imagecreatetruecolor($w, $h);
    // 逆側から色を取得
    for ($i=($w-1);$i>=0;$i--) {
        for ($j=0;$j<$h;$j++) {
            $color_index = imagecolorat($image, $i, $j);
            $colors = imagecolorsforindex($image, $color_index);
            imagesetpixel($destImage, abs($i-$w+1), $j, imagecolorallocate($destImage, $colors["red"], $colors["green"], $colors["blue"]));
        }
    }
    return $destImage;
}
// 上下反転
function image_flip($image)
{
    // 画像の幅を取得
    $w = imagesx($image);
    // 画像の高さを取得
    $h = imagesy($image);
    // 変換後の画像の生成（元の画像と同じサイズ）
    $destImage = @imagecreatetruecolor($w, $h);
    // 逆側から色を取得
    for ($i=0;$i<$w;$i++) {
        for ($j=($h-1);$j>=0;$j--) {
            $color_index = imagecolorat($image, $i, $j);
            $colors = imagecolorsforindex($image, $color_index);
            imagesetpixel($destImage, $i, abs($j-$h+1), imagecolorallocate($destImage, $colors["red"], $colors["green"], $colors["blue"]));
        }
    }
    return $destImage;
}
// 画像を回転
function image_rotate($image, $angle, $bgd_color)
{
    return imagerotate($image, $angle, $bgd_color, 0);
}

// 画像の方向を正す
function orientationFixedImage($output, $input)
{
    $image = ImageCreateFromJPEG($input);
    $exif_datas = @exif_read_data($input);
    if (isset($exif_datas['Orientation'])) {
        $orientation = $exif_datas['Orientation'];
        if ($image) {
            // 未定義
            if ($orientation == 0) {

            // 通常
            } elseif ($orientation == 1) {

            // 左右反転
            } elseif ($orientation == 2) {
                $image = image_flop($image);
                // 180°回転
            } elseif ($orientation == 3) {
                $image = image_rotate($image, 180, 0);
                // 上下反転
            } elseif ($orientation == 4) {
                $image = image_flip($image);
                // 反時計回りに90°回転 上下反転
            } elseif ($orientation == 5) {
                $image = image_rotate($image, 90, 0);
                $image = image_flip($image);
                // 反時計回りに270°回転
            } elseif ($orientation == 6) {
                $image = image_rotate($image, 270, 0);
                // 反時計回りに270°回転 上下反転
            } elseif ($orientation == 7) {
                $image = image_rotate($image, 270, 0);
                $image = image_flip($image);
                // 反時計回りに90°回転
            } elseif ($orientation == 8) {
                $image = image_rotate($image, 90, 0);
            }
        }
    }
    // 画像の書き出し
    ImageJPEG($image, $output);
    return $image;
}
