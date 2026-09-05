<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row admin-shell">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">操作记录</h1>
            </div>
            <div class="panel-body">
                <?php $resourceLabels = ['topic' => '主题', 'comment' => '回复', 'node' => '节点', 'user' => '用户', 'report' => '举报'];
$actionLabels = ['edit' => '编辑', 'author_edit' => '作者修改', 'flags' => '修改置顶/锁帖', 'save' => '保存设置', 'mute' => '禁言', 'unmute' => '解除禁言', 'resolved' => '完成举报处理']; ?>

                <?php foreach ($rows as $row): ?>
                    <article class="border-bottom py-2">
                        <div><?= esc($row['username'] ?? '系统或已删除用户') ?> ·
                            <?= esc($resourceLabels[$row['resource_type']] ?? $row['resource_type']) ?>
                            #<?= (int) $row['resource_id'] ?> ·
                            <?= esc($actionLabels[$row['action']] ?? content_status($row['action'])) ?></div>
                        <div class="meta"><?= forum_date($row['created_at']) ?></div>
                        <p><?= esc($row['reason']) ?></p>
                    </article>
                <?php endforeach ?>

                <?php if (!$rows): ?>
                    <p>暂无操作记录。</p>
                <?php endif ?>
            </div>
        </div><?= forum_pager('/admin/logs', $page, $hasNext) ?>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
