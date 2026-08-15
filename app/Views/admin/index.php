<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h1 class="h4 mb-3">后台管理</h1>
<div class="row g-3">
    <div class="col-md-5">
        <div class="panel p-3 mb-3">
            <h2 class="h5">新增节点</h2>
            <form method="post" action="/admin/node">
                <?= csrf_field() ?>
                <input class="form-control mb-2" name="name" placeholder="名称" required><input class="form-control mb-2"
                    name="slug" placeholder="slug" pattern="[a-z0-9-]+" required><input class="form-control mb-2"
                    name="description" placeholder="说明"><input class="form-control mb-2" type="number" name="sort_order"
                    value="0"><button class="btn btn-primary">创建</button>
            </form>
            <hr>
            <?php foreach ($nodes as $n):?>
                <div><?= esc($n['name']) ?> <span class="meta"><?= esc($n['slug']) ?></span></div>
            <?php endforeach?>
        </div>
        <div class="panel p-3">
            <h2 class="h5">站点设置</h2>
            <form method="post" action="/admin/settings">
                <?= csrf_field() ?>
                <input class="form-control mb-2" name="site_name" placeholder="站点名称" maxlength="80"
                    value="<?= esc(old('site_name', $settings['site_name'] ?? 'LetsBBS')) ?>" required><input
                    class="form-control mb-2" name="site_description" placeholder="站点说明" maxlength="160"
                    value="<?= esc(old('site_description', $settings['site_description'] ?? '')) ?>"><button
                    class="btn btn-outline-primary">保存</button>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="panel mb-3">
            <div class="p-3 border-bottom">
                <h2 class="h5 mb-0">待处理主题</h2>
            </div>
            <?php if (!$pendingTopics):?>
                <p class="p-3 text-secondary mb-0">暂无待处理主题。</p>
            <?php endif?>

            <?php foreach ($pendingTopics as $t):?>
                <div class="topic-row"><a href="/topic/<?= $t['id'] ?>"><?= esc($t['title']) ?></a><span class="meta"> ·
                        <?= esc($t['username']) ?> · <?= esc($t['status']) ?></span>
                    <form class="mt-2 d-flex gap-2" method="post" action="/admin/topic/<?= $t['id'] ?>/moderate">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-success" name="status" value="published">通过</button><button
                            class="btn btn-sm btn-warning" name="status" value="hidden">隐藏</button><button
                            class="btn btn-sm btn-danger" name="status" value="deleted">删除</button>
                    </form>
                </div>
            <?php endforeach?>
        </div>
        <div class="panel mb-3">
            <div class="p-3 border-bottom">
                <h2 class="h5 mb-0">待处理回复</h2>
            </div>
            <?php if (!$pendingComments):?>
                <p class="p-3 text-secondary mb-0">暂无待处理回复。</p>
            <?php endif?>

            <?php foreach ($pendingComments as $c):?>
                <div class="topic-row"><a href="/topic/<?= $c['topic_id'] ?>"><?= esc($c['title']) ?></a><span
                        class="meta">
                        · <?= esc($c['username']) ?></span>
                    <div class="post-body mt-2"><?= $c['body'] ?></div>
                    <form class="mt-2 d-flex gap-2" method="post" action="/admin/comment/<?= $c['id'] ?>/moderate">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-success" name="status" value="published">通过</button><button
                            class="btn btn-sm btn-danger" name="status" value="deleted">删除</button>
                    </form>
                </div>
            <?php endforeach?>
        </div>
        <div class="panel">
            <div class="p-3 border-bottom">
                <h2 class="h5 mb-0">最近用户</h2>
            </div>
            <?php foreach ($users as $u):?>
                <div class="topic-row d-flex justify-content-between"><a
                        href="/member/<?= esc($u['username'], 'url') ?>"><?= esc($u['username']) ?></a>
                    <form method="post" action="/admin/member/<?= $u['id'] ?>/mute">
                        <?= csrf_field() ?>
                        <button
                            class="btn btn-sm <?= $u['is_muted'] ? 'btn-success' : 'btn-outline-danger' ?>"><?= $u['is_muted'] ? '解除禁言' : '禁言' ?></button>
                    </form>
                </div>
            <?php endforeach?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
