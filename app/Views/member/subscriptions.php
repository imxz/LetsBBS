<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">收藏与关注</h1>
            </div>
            <div class="panel-body">
                <p><a href="/my/subscriptions?kind=topics">主题</a> · <a href="/my/subscriptions?kind=nodes">节点</a> · <a
                        href="/my/subscriptions?kind=users">用户</a> · <a href="/settings/notifications">通知偏好</a></p>
                <?php foreach ($rows as $row): ?>
                    <?php $path = match ($kind) {
                        'nodes' => '/node/' . rawurlencode($row['slug']), 'users' => '/member/' . rawurlencode($row['label']), default => '/topic/' . $row['id']
                    };
                    $action = match ($kind) {
                        'nodes' => '/node/' . $row['id'], 'users' => '/member/' . rawurlencode($row['label']), default => '/topic/' . $row['id']
                    }; ?>
                    <article class="d-flex justify-content-between align-items-center border-bottom py-3"><a
                            href="<?= esc($path) ?>"><?= esc($row['label']) ?></a>
                        <form method="post" action="<?= esc($action) ?>/follow">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-secondary">取消关注</button>
                        </form>
                    </article>
                <?php endforeach ?>

                <?php if (!$rows): ?>
                    <p>暂无收藏或关注。</p>
                <?php endif ?>
            </div>
        </div><?= forum_pager('/my/subscriptions', $page, $hasNext, ['kind' => $kind]) ?>
    </section><?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
