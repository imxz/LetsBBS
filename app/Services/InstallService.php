<?php

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use RuntimeException;

final class InstallService
{
    public function install(string $token, string $username, string $password): void
    {
        $expected = (string) env('INSTALL_TOKEN', '');
        if ($expected === '' || ! hash_equals($expected, $token)) {
            throw new RuntimeException('安装令牌错误。');
        }
        if (! preg_match('/^[a-z0-9]{3,12}$/', $username) || strlen($password) < 12) {
            throw new RuntimeException('管理员用户名或密码不符合要求。');
        }

        $mutex = fopen(WRITEPATH . 'install.mutex', 'c+');
        if (! $mutex || ! flock($mutex, LOCK_EX | LOCK_NB)) {
            throw new RuntimeException('另一个安装进程正在运行。');
        }
        try {
            if (is_file(WRITEPATH . 'install.lock')) {
                throw new RuntimeException('站点已经安装。');
            }
            $db = db_connect();
            if ($db->DBDriver !== 'MySQLi' || strtolower((string) $db->username) === 'root') {
                throw new RuntimeException('必须使用 MySQLi 和非 root 应用账号。');
            }
            $version = (string) ($db->query('SELECT VERSION() AS version')->getRowArray()['version'] ?? '');
            if (! str_starts_with($version, '8.4.')) {
                throw new RuntimeException("要求 MySQL 8.4，当前为 {$version}。");
            }
            $statePath = WRITEPATH . 'install.state';
            $resuming = is_file($statePath);
            if (! $resuming) {
                $count = (int) ($db->query("SELECT COUNT(*) AS n FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name <> 'migrations'")->getRowArray()['n'] ?? 0);
                if ($count > 0) {
                    throw new RuntimeException('数据库非空，拒绝首次安装。');
                }
                file_put_contents($statePath, json_encode(['stage' => 'migrate','started_at' => gmdate(DATE_ATOM)]), LOCK_EX);
            }
            $migrations = service('migrations');
            $migrations->setNamespace('CodeIgniter\\Shield')->latest();
            $migrations->setNamespace('CodeIgniter\\Settings')->latest();
            $migrations->setNamespace('App')->latest();
            file_put_contents($statePath, json_encode(['stage' => 'seed']), LOCK_EX);
            \Config\Database::seeder()->call('App\\Database\\Seeds\\InitialSeeder');

            $provider = auth()->getProvider();
            $existing = $provider->findByCredentials(['username' => $username]);
            if (! $existing) {
                $user = new User(['username' => $username,'email' => $username . '@local.invalid','password' => $password,'active' => 1]);
                if (! $provider->save($user)) {
                    throw new RuntimeException(implode('；', $provider->errors()));
                }
                $existing = $provider->findById($provider->getInsertID());
            }
            if (! $existing->inGroup('admin')) {
                $existing->addGroup('admin');
            }
            $now = gmdate('Y-m-d H:i:s');
            $db->table('user_profiles')->ignore(true)->insert(['user_id' => $existing->id,'created_at' => $now,'updated_at' => $now]);
            file_put_contents(WRITEPATH . 'install.lock', json_encode(['installed_at' => gmdate(DATE_ATOM),'version' => '4.7.4'], JSON_UNESCAPED_SLASHES), LOCK_EX);
            if (is_file($statePath)) {
                unlink($statePath);
            }
        } finally {
            flock($mutex, LOCK_UN);
            fclose($mutex);
        }
    }
}
