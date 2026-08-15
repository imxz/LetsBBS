<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

final class SiteSettings
{
    /** @var array<string, string>|null */
    private ?array $values = null;

    public function __construct(private ?BaseConnection $db = null)
    {
        $this->db ??= db_connect();
    }

    public function get(string $key, string $default = ''): string
    {
        $this->load();

        return $this->values[$key] ?? $default;
    }

    /** @return array<string, string> */
    public function all(): array
    {
        $this->load();

        return $this->values ?? [];
    }

    /** @param array<string, string> $values */
    public function save(array $values): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->transException(true)->transBegin();
        try {
            foreach ($values as $key => $value) {
                $this->db->table('site_settings')->replace([
                    'setting_key' => $key,
                    'setting_value' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                    'updated_at' => $now,
                ]);
            }
            $this->db->transCommit();
            $this->values = null;
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function load(): void
    {
        if ($this->values !== null) {
            return;
        }

        $this->values = [];
        foreach ($this->db->table('site_settings')->get()->getResultArray() as $row) {
            $value = json_decode((string) $row['setting_value'], true);
            if (is_string($value)) {
                $this->values[(string) $row['setting_key']] = $value;
            }
        }
    }
}
