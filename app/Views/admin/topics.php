<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="p-3 border-bottom">
                <h1 class="h5 mb-0"><?= esc($title) ?></h1>
            </div>
            <div class="p-3 table-responsive">
                <form class="d-flex gap-2 mb-3" method="get">
                    <input class="form-control" name="q" maxlength="80" placeholder="搜索标题"
                        value="<?= esc($query) ?>"><button class="btn btn-outline-secondary text-nowrap">提交</button>
                </form>
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>标题</th>
                            <th>作者</th>
                            <th>发布时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topics as $topic):?>
                            <tr>
                                <td><?= (int) $topic['id'] ?></td>
                                <td><a href="/topic/<?= (int) $topic['id'] ?>"
                                        target="_blank"><?= esc($topic['title']) ?></a>
                                    <div class="meta"><?= esc($topic['node_name']) ?> · <?= esc($topic['status']) ?>
                                    </div>
                                </td>
                                <td><a href="/member/<?= esc($topic['username'], 'url') ?>"
                                        target="_blank"><?= esc($topic['username']) ?></a></td>
                                <td class="meta text-nowrap"><?= esc($topic['created_at']) ?></td>
                                <td class="text-nowrap">
                                    <a href="/admin/topic/<?= (int) $topic['id'] ?>/edit">编辑</a>
                                    <form class="d-inline" method="post"
                                        action="/admin/topic/<?= (int) $topic['id'] ?>/moderate">
                                        <?= csrf_field() ?>

                                        <?php if ($topic['status'] === 'hidden'):?>
                                            <button class="btn btn-sm btn-link text-success p-0" name="status"
                                                value="published">通过</button>
                                        <?php endif?>
                                        <button class="btn btn-sm btn-link text-danger p-0" name="status"
                                            value="deleted" onclick="return confirm('确实要删除吗？')">删除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach?>

                        <?php if (!$topics):?>
                            <tr>
                                <td colspan="5" class="text-secondary text-center py-4">暂无数据。</td>
                            </tr>
                        <?php endif?>
                    </tbody>
                </table>
            </div>
        </div>
        <nav class="mt-3 d-flex justify-content-between" aria-label="后台主题分页">
            <?= $page > 1 ? '<a class="btn btn-outline-secondary" href="' . $basePath . '/' . ($page - 1) . ($query !== '' ? '?q=' . urlencode($query) : '') . '">上一页</a>' : '<span></span>' ?>

            <?php if ($hasNext):?>
                <a class="btn btn-outline-secondary"
                    href="<?= $basePath ?>/<?= $page + 1 ?><?= $query !== '' ? '?q=' . urlencode($query) : '' ?>">下一页</a>
            <?php endif?>
        </nav>
        <?php if ($section === 'topic-verify'):?>
            <div class="panel mt-4">
                <div class="p-3 border-bottom">
                    <h2 class="h5 mb-0">待审核回复</h2>
                </div>
                <?php foreach ($pendingComments as $comment):?>
                    <article class="topic-row">
                        <div><a href="/topic/<?= (int) $comment['topic_id'] ?>"><?= esc($comment['title']) ?></a> · <a
                                href="/member/<?= esc($comment['username'], 'url') ?>"><?= esc($comment['username']) ?></a>
                        </div>
                        <div class="post-body my-2"><?= $comment['body'] ?></div>
                        <form class="d-flex gap-2" method="post"
                            action="/admin/comment/<?= (int) $comment['id'] ?>/moderate">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-success" name="status" value="published">通过</button><button
                                class="btn btn-sm btn-outline-danger" name="status" value="deleted">删除</button>
                        </form>
                    </article>
                <?php endforeach?>

                <?php if (!$pendingComments):?>
                    <p class="p-3 text-secondary mb-0">暂无待审核回复。</p>
                <?php endif?>
            </div>
        <?php endif?>
    </section>
    <?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
