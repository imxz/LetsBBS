<?= $this->extend('layouts/main') ?>

<?= $this->setData(['editor' => auth()->loggedIn()]) ?>

<?= $this->section('content') ?>
<article class="panel p-4 mb-3">
    <div class="d-flex justify-content-between">
        <div>
            <div class="meta"><a href="/node/<?= $topic['node_id'] ?>"><?= esc($topic['node_name']) ?></a></div>
            <h1 class="h4 mt-2"><?= esc($topic['title']) ?></h1>
            <div class="meta">由 <a
                    href="/member/<?= esc($topic['username'], 'url') ?>"><?= esc($topic['username']) ?></a> 发布于
                <?= esc($topic['created_at']) ?></div>
        </div>
        <?php if (auth()->loggedIn()):?>
            <div class="d-flex gap-2">
                <form method="post" action="/topic/<?= $topic['id'] ?>/follow">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-primary"><?= $following ? '取消关注' : '关注' ?></button>
                </form>
                <?php if (auth()->id() == $topic['user_id'] || auth()->user()->inGroup('admin')):?>
                    <form method="post" action="/topic/<?= $topic['id'] ?>/delete">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-danger">删除</button>
                    </form>
                <?php endif?>
            </div>
        <?php endif?>
    </div>
    <div class="post-body mt-4"><?= $topic['body'] ?></div>
</article>
<?php foreach ($comments as $i => $c):?>
    <article class="panel p-3 mb-2">
        <div class="meta"><a href="/member/<?= esc($c['username'], 'url') ?>"><?= esc($c['username']) ?></a> ·
            #<?= $i + 1 ?> · <?= esc($c['created_at']) ?></div>
        <div class="post-body mt-2"><?= $c['body'] ?></div>
        <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')):?>
            <form class="mt-2" method="post" action="/admin/comment/<?= $c['id'] ?>/moderate">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-outline-danger" name="status" value="deleted">删除回复</button>
            </form>
        <?php endif?>
    </article>
<?php endforeach?>

<?php if (auth()->loggedIn()):?>
    <div class="panel p-3 mt-3">
        <form method="post" action="/topic/<?= $topic['id'] ?>/comment">
            <?= csrf_field() ?>
            <textarea class="form-control js-editor" name="body" required></textarea><button
                class="btn btn-primary mt-3">回复</button>
        </form>
    </div>
<?php else:?>
    <div class="alert alert-light border mt-3"><a href="/login">登录</a>后参与回复。</div>
<?php endif?>

<?= $this->endSection() ?>
