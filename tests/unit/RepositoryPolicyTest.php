<?php

use CodeIgniter\Test\CIUnitTestCase;

final class RepositoryPolicyTest extends CIUnitTestCase
{
    public function testFrontendArtifactsArePartOfTheApplication(): void
    {
        foreach (
            [
                'VERSIONS.json',
                'SHA256SUMS',
                'bootstrap/css/bootstrap.min.css',
                'bootstrap/js/bootstrap.bundle.min.js',
                'tinymce/tinymce.min.js',
                'tinymce/icons/default/icons.min.js',
                'tinymce/models/dom/model.min.js',
                'tinymce/themes/silver/theme.min.js',
                'tinymce/skins/ui/oxide/skin.min.css',
                'tinymce/skins/ui/oxide/content.min.css',
                'tinymce/skins/content/default/content.min.css',
            ]
            as $artifact
        ) {
            $this->assertFileExists(FCPATH . 'static/vendor/' . $artifact);
        }

        $ignore = file_get_contents(HOMEPATH . '.gitignore');
        $this->assertStringContainsString('/vendor/', $ignore);
        $this->assertStringNotContainsString("\nvendor/", $ignore);
    }

    public function testFrontendVendorDirectoryContainsOnlyProductionArtifacts(): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(FCPATH . 'static/vendor/tinymce', FilesystemIterator::SKIP_DOTS),
        );

        foreach ($files as $file) {
            $name = $file->getFilename();
            $this->assertNotSame('ts', $file->getExtension(), $file->getPathname());
            $this->assertNotSame('index.js', $name, $file->getPathname());

            if (in_array($file->getExtension(), ['css', 'js'], true)) {
                $this->assertStringContainsString('.min.', $name, $file->getPathname());
            }
        }
    }

    public function testFrontendArtifactChecksumsAreCompleteAndValid(): void
    {
        $root = FCPATH . 'static/vendor/';
        $listed = [];

        foreach (file($root . 'SHA256SUMS', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $this->assertMatchesRegularExpression('/^[a-f0-9]{64}  .+$/', $line);
            [$expectedHash, $relativePath] = explode('  ', $line, 2);
            $listed[] = $relativePath;

            $this->assertFileExists($root . $relativePath);
            $this->assertSame($expectedHash, hash_file('sha256', $root . $relativePath), $relativePath);
        }

        $actual = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($files as $file) {
            $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen($root)));
            if ($relativePath !== 'SHA256SUMS') {
                $actual[] = $relativePath;
            }
        }

        sort($actual);
        sort($listed);
        $this->assertSame($actual, $listed);
    }

    public function testDockerContextExcludesSecretsAndRuntimeData(): void
    {
        $ignore = file_get_contents(HOMEPATH . '.dockerignore');
        foreach (
            [
                '.env',
                '.env.*',
                '.legacy-preview',
                '.vscode',
                'build',
                'node_modules',
                'vendor/**',
                'writable/logs/*',
                'public/uploads/*',
            ]
            as $entry
        ) {
            $this->assertStringContainsString($entry, $ignore);
        }
    }

    public function testSharedEditorConfigurationIsPortable(): void
    {
        if (!is_dir(HOMEPATH . '.vscode')) {
            $this->assertDirectoryDoesNotExist(HOMEPATH . '.vscode');

            return;
        }

        $settings = file_get_contents(HOMEPATH . '.vscode/settings.json');
        $tasks = file_get_contents(HOMEPATH . '.vscode/tasks.json');

        $this->assertDoesNotMatchRegularExpression('/[A-Z]:\\\\/', $settings . $tasks);
        $this->assertStringNotContainsString('npm.cmd', $settings . $tasks);
    }
}
