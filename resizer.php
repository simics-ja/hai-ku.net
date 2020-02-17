<?php
function roundresizer($round_size, $in, $out)
{
    try {
        if (!$info = getimagesize($in)) {
            throw new RuntimeException("Invalid File: unable to get size of the image.");
        }
        // 縦横比を維持したまま 120 * 120 以下に収まるサイズを求める
        if ($info[0] >= $info[1]) {
            $dst_w = $round_size;
            $dst_h = ceil($round_size * $info[1] / max($info[0], 1));
        } else {
            $dst_w = ceil($round_size * $info[0] / max($info[1], 1));
            $dst_h = $round_size;
        }
        // リサンプリング先画像リソースを生成する
        $dst = imagecreatetruecolor($dst_w, $dst_h);
        $src = imagecreatefromjpeg($in);
        // getimagesize関数で得られた情報も利用してリサンプリングを行う
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $dst_w, $dst_h, $info[0], $info[1]);

        if (!$output = imagejpeg($dst, $out)) {
            throw new RuntimeException("Error in saving image.");
        }
        // ファイルのパーミッションを確実に0644に設定する
        chmod($out, 0644);
        imagedestroy($dst);
        return $out;
    } catch (RuntimeException $e) {
        echo "<br /><br /><br />" . $e->getMessage();
        return null;
    }
}
