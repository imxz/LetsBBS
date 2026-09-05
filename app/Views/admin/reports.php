<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row admin-shell">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">举报处理</h1>
            </div>
            <div class="panel-body">
                <p><a href="/admin/reports?status=open">待处理</a> · <a href="/admin/reports?status=resolved">已处理</a></p>
                <?php foreach ($rows as $row): ?>
                    <article class="border-bottom py-3"><a
                            href="<?= esc($row['comment_id'] ? reply_url((int) $row['topic_id'], (int) $row['comment_id']) : '/topic/' . $row['topic_id']) ?>"><?= esc($row['title']) ?></a>
                        <div class="meta"><?= esc($row['username']) ?> · <?= forum_date($row['created_at']) ?></div>
                        <p><?= esc($row['reason']) ?></p>
                        <?php if ($statusFilter === 'open'): ?>
                            <form method="post" action="/admin/report/<?= (int) $row['id'] ?>/resolve">
                                <?= csrf_field() ?>
                                <input class="form-control mb-2" name="reason" maxlength="500"
                                    placeholder="处理说明"><button class="btn btn-sm btn-primary">标记处理完成</button>
                            </form>
                        <?php endif ?>
                    </article>
                <?php endforeach ?>

                <?php if (!$rows): ?>
                    <p>暂无举报。</p>
                <?php endif ?>
            </div>
        </div><?= forum_pager('/admin/reports', $page, $hasNext, ['status' => $statusFilter]) ?>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
