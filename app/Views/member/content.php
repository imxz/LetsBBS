<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">我的内容</h1>
            </div>
            <div class="panel-body">
                <form class="d-flex gap-2 mb-3" method="get"><select class="form-select" name="kind" aria-label="内容类型">
                        <option value="topics" <?= $kind === 'topics' ? 'selected' : '' ?>>主题</option>
                        <option value="comments" <?= $kind === 'comments' ? 'selected' : '' ?>>回复</option>
                    </select><select class="form-select" name="status" aria-label="内容状态">
                        <option value="">全部状态</option>
                        <?php foreach (['published','pending','hidden','deleted'] as $state): ?>
                            <option value="<?= $state ?>" <?= $statusFilter === $state ? 'selected' : '' ?>>
                                <?= content_status($state) ?></option>
                        <?php endforeach ?>
                    </select><button class="btn btn-primary">筛选</button></form>
                <?php foreach ($rows as $row): ?>
                    <article class="border-bottom py-3">
                        <h2 class="h6"><?= esc($row['title'] ?? '我的回复') ?> · <?= content_status($row['status']) ?></h2>
                        <div class="meta"><?= forum_date($row['created_at']) ?></div>
                        <div class="post-body my-2"><?= $row['body'] ?></div>
                        <?php if ($row['moderation_reason']): ?>
                            <p class="alert alert-info">处理说明：<?= esc($row['moderation_reason']) ?></p>
                        <?php endif ?>

                        <?php if ($row['publicly_visible']): ?>
                            <a class="me-3"
                                href="<?= esc($kind === 'topics' ? '/topic/' . $row['id'] : reply_url((int) $row['topic_id'], (int) $row['id'])) ?>">查看公开页面</a>
                        <?php endif ?>

                        <?php if (in_array($row['status'], ['published','pending'], true) && strtotime($row['created_at'] . ' UTC') >= time() - 1800): ?>
                            <a href="/my/<?= $kind ?>/<?= (int) $row['id'] ?>/edit">修改内容</a>
                        <?php endif ?>
                    </article>
                <?php endforeach ?>

                <?php if (!$rows): ?>
                    <p>暂无内容。</p>
                <?php endif ?>
            </div>
        </div><?= forum_pager('/my/content', $page, $hasNext, ['kind' => $kind, 'status' => $statusFilter]) ?>
    </section><?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
