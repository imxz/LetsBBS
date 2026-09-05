<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row admin-shell">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">回复管理</h1>
            </div>
            <div class="panel-body">
                <form class="d-flex gap-2 mb-3" method="get"><select class="form-select" name="status"
                        aria-label="回复状态">
                        <?php foreach (['pending','published','hidden','deleted'] as $state): ?>
                            <option value="<?= $state ?>" <?= $statusFilter === $state ? 'selected' : '' ?>>
                                <?= content_status($state) ?></option>
                        <?php endforeach ?>
                    </select><input class="form-control" name="q" value="<?= esc($query) ?>"
                        placeholder="正文、标题或作者"><button class="btn btn-primary">筛选</button></form>
                <?php foreach ($rows as $row): ?>
                    <article class="border-bottom py-3">
                        <div><a
                                href="<?= esc(reply_url((int) $row['topic_id'], (int) $row['id'])) ?>"><?= esc($row['title']) ?></a>
                            · <?= esc($row['username']) ?> · #<?= (int) $row['id'] ?></div>
                        <div class="meta"><?= forum_date($row['created_at']) ?> ·
                            主题<?= content_status($row['topic_status']) ?></div>
                        <div class="post-body my-2"><?= $row['body'] ?></div>
                        <p class="meta"><?= esc($row['moderation_reason']) ?></p>
                        <?= view('admin/_moderate', ['row' => $row, 'resource' => 'comment']) ?>
                    </article>
                <?php endforeach ?>

                <?php if (!$rows): ?>
                    <p>暂无此状态的回复。</p>
                <?php endif ?>
            </div>
        </div><?= forum_pager('/admin/comment/page', $page, $hasNext, ['status' => $statusFilter, 'q' => $query]) ?>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
