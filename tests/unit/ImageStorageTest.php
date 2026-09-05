<?php

use App\Services\ImageStorage;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Test\CIUnitTestCase;

final class ImageStorageTest extends CIUnitTestCase
{
    public function testLargeAvatarIsCroppedAndResized(): void
    {
        $temp = tempnam(sys_get_temp_dir(), 'avatar-test-');
        $source = imagecreatetruecolor(1800, 900);
        imagepng($source, $temp);
        imagedestroy($source);
        $file = $this->getMockBuilder(UploadedFile::class)
            ->setConstructorArgs([$temp, 'avatar.png', 'image/png', filesize($temp), UPLOAD_ERR_OK])
            ->onlyMethods(['isValid'])
            ->getMock();
        $file->method('isValid')->willReturn(true);
        $path = null;
        try {
            $path = new ImageStorage()->store($file, 'avatars');
            $size = getimagesize(FCPATH . ltrim($path, '/'));
            $this->assertSame(256, $size[0]);
            $this->assertSame(256, $size[1]);
            $this->assertSame('image/png', $size['mime']);
        } finally {
            unlink($temp);
            if ($path) {
                new ImageStorage()->delete($path, 'avatars');
            }
        }
    }
}
