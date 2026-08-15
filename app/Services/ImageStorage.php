<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

final class ImageStorage
{
    private const MIMES = ['image/jpeg' => 'jpg','image/png' => 'png','image/gif' => 'gif','image/webp' => 'webp'];

    public function store(?UploadedFile $file, string $bucket = 'editor', int $maxDimension = 6000, int $maxBytes = 5242880): string
    {
        if (! in_array($bucket, ['editor', 'avatars'], true)) {
            throw new \InvalidArgumentException('无效的图片存储目录。');
        }
        if ($file && in_array($file->getError(), [UPLOAD_ERR_INI_SIZE,UPLOAD_ERR_FORM_SIZE], true)) {
            throw new RuntimeException('图片超过大小限制。', 413);
        }
        if (!$file || !$file->isValid()) {
            throw new RuntimeException('没有收到有效图片。');
        }
        if ($file->getSize() > $maxBytes) {
            throw new RuntimeException('图片超过大小限制。', 413);
        }
        $tmp = $file->getTempName();
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if (!isset(self::MIMES[$mime])) {
            throw new RuntimeException('仅支持 JPEG、PNG、GIF 和 WebP。', 422);
        }
        $size = @getimagesize($tmp);
        if (!$size || $size[0] < 1 || $size[1] < 1 || $size[0] > $maxDimension || $size[1] > $maxDimension) {
            throw new RuntimeException('图片尺寸无效或过大。', 422);
        }
        $image = @imagecreatefromstring((string) file_get_contents($tmp));
        if (!$image) {
            throw new RuntimeException('图片内容无法解码。', 422);
        }
        $folder = $bucket === 'editor' ? 'editor/' . gmdate('Y/m') : 'avatars';
        $absolute = FCPATH . 'uploads/' . $folder;
        if (!is_dir($absolute) && !mkdir($absolute, 0755, true) && !is_dir($absolute)) {
            imagedestroy($image);
            throw new RuntimeException('上传目录不可写。');
        }
        $ext = self::MIMES[$mime];
        $name = bin2hex(random_bytes(20)) . '.' . $ext;
        $target = $absolute . '/' . $name;
        if (in_array($mime, ['image/png','image/gif','image/webp'], true)) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }
        $ok = match ($mime) {
            'image/jpeg' => imagejpeg($image, $target, 88),'image/png' => imagepng($image, $target, 6),'image/gif' => imagegif($image, $target),'image/webp' => imagewebp($image, $target, 86)
        };
        imagedestroy($image);
        if (!$ok) {
            throw new RuntimeException('图片保存失败。');
        } @chmod($target, 0644);
        return '/uploads/' . $folder . '/' . $name;
    }

    public function delete(string $publicPath, string $bucket): void
    {
        if ($bucket !== 'avatars') {
            return;
        }
        $prefix = '/uploads/' . $bucket . '/';
        if (! str_starts_with($publicPath, $prefix)) {
            return;
        }
        $relative = substr($publicPath, strlen('/uploads/'));
        if (! preg_match('#\Aavatars/[a-f0-9]{40}\.(?:jpg|png|gif|webp)\z#', $relative)) {
            return;
        }
        $path = FCPATH . 'uploads/' . $relative;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
