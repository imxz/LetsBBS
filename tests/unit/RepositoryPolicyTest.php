<?php

use CodeIgniter\Test\CIUnitTestCase;

final class RepositoryPolicyTest extends CIUnitTestCase
{
    public function testFrontendArtifactsArePartOfTheApplication(): void
    {
        $this->assertFileExists(FCPATH . 'static/vendor/VERSIONS.json');
        $this->assertFileExists(FCPATH . 'static/vendor/bootstrap/css/bootstrap.min.css');
        $this->assertFileExists(FCPATH . 'static/vendor/tinymce/tinymce.min.js');

        $ignore = file_get_contents(HOMEPATH . '.gitignore');
        $this->assertStringContainsString('/vendor/', $ignore);
        $this->assertStringNotContainsString("\nvendor/", $ignore);
    }

    public function testDockerContextExcludesSecretsAndRuntimeData(): void
    {
        $ignore = file_get_contents(HOMEPATH . '.dockerignore');
        foreach (['.env', 'build', 'vendor/**', 'writable/logs/*', 'public/uploads/*'] as $entry) {
            $this->assertStringContainsString($entry, $ignore);
        }
    }
}
