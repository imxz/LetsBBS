<div class="meta mt-1"><a href="/node/<?= esc($item['node_slug'], 'url') ?>"><?= esc($item['node_name']) ?></a> ·
    <?= esc(relative_time($item['last_activity_at'])) ?> ·
    <?= $item['last_reply_username'] ? '最后回复来自 ' . esc($item['last_reply_username']) : '暂无回复' ?> · <a
        href="/topic/<?= (int) $item['id'] ?>?page=<?= max(1, (int) ceil($item['comment_count'] / 30)) ?>#reply-<?= (int) $item['comment_count'] ?>"><?= (int) $item['comment_count'] ?>
        回复</a></div>
