<?php helper('forum'); ?>

<?= $this->extend('layouts/main') ?>

<?php $this->setData(['editor' => auth()->loggedIn()]) ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <article class="panel mb-3">
            <header class="panel-heading topic-detail-heading">
                <div class="d-flex justify-content-between gap-3">
                    <div class="min-w-0">
                        <div class="meta"><a href="/">首页</a> / <a
                                href="/node/<?= (int) $topic['node_id'] ?>"><?= esc($topic['node_name']) ?></a></div>
                        <h1 class="topic-detail-title"><?= esc($topic['title']) ?></h1>
                        <div class="meta">By <a
                                href="/member/<?= esc($topic['username'], 'url') ?>"><?= esc($topic['username']) ?></a>
                            · <?= esc($topic['created_at']) ?> · <?= (int) $topic['view_count'] ?> 次点击
                            <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')):?>
                                · <a href="/admin/topic/<?= (int) $topic['id'] ?>/edit">编辑</a>
                            <?php endif?>
                        </div>
                    </div>
                    <a class="avatar topic-avatar flex-shrink-0" href="/member/<?= esc($topic['username'], 'url') ?>">
                        <?php if ($topic['avatar']):?>
                            <img class="avatar topic-avatar" src="<?= esc($topic['avatar']) ?>"
                                alt="<?= esc($topic['username']) ?> 的头像">
                        <?php else:?>
                            <?= esc(strtoupper(substr($topic['username'], 0, 1))) ?>

                        <?php endif?>
                    </a>
                </div>
            </header>
            <div class="post-body panel-body"><?= $topic['body'] ?></div>
            <footer class="panel-footer d-flex justify-content-between align-items-center">
                <?php if (auth()->loggedIn()):?>
                    <form method="post" action="/topic/<?= (int) $topic['id'] ?>/follow">
                        <?= csrf_field() ?>
                        <button
                            class="btn btn-sm btn-link text-secondary p-0"><?= $following ? '取消关注' : '关注主题' ?></button>
                    </form>
                <?php else:?>
                    <a class="small text-secondary" href="/login">登录后关注主题</a>
                <?php endif?>

                <?php if (auth()->loggedIn() && (auth()->id() == $topic['user_id'] || auth()->user()->inGroup('admin'))):?>
                    <form method="post" action="/topic/<?= (int) $topic['id'] ?>/delete">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-link text-danger p-0"
                            onclick="return confirm('确实要删除吗？')">删除</button>
                    </form>
                <?php endif?>
            </footer>
        </article>

        <section class="panel mb-3">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title"><small><?= (int) $topic['comment_count'] ?> 回复 · 截至现在</small></h2><a
                    class="small text-secondary" href="#Reply">添加回复</a>
            </header>
            <?php foreach ($comments as $i => $comment):?>
                <article id="reply-<?= $i + 1 ?>" class="topic-row d-flex gap-3">
                    <a class="avatar flex-shrink-0" href="/member/<?= esc($comment['username'], 'url') ?>">
                        <?php if ($comment['avatar']):?>
                            <img class="avatar" src="<?= esc($comment['avatar']) ?>"
                                alt="<?= esc($comment['username']) ?> 的头像">
                        <?php else:?>
                            <?= esc(strtoupper(substr($comment['username'], 0, 1))) ?>

                        <?php endif?>
                    </a>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between gap-3">
                            <div class="meta"><a
                                    href="/member/<?= esc($comment['username'], 'url') ?>"><?= esc($comment['username']) ?></a>
                                · <?= esc(relative_time($comment['created_at'])) ?></div><button
                                class="btn btn-sm btn-link text-secondary p-0 js-reply-to" type="button"
                                data-username="<?= esc($comment['username']) ?>">#<?= $i + 1 ?> ↩</button>
                        </div>
                        <div class="post-body mt-2"><?= $comment['body'] ?></div>
                        <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')):?>
                            <form class="mt-2" method="post"
                                action="/admin/comment/<?= (int) $comment['id'] ?>/moderate">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-link text-danger p-0" name="status"
                                    value="deleted">删除回复</button>
                            </form>
                        <?php endif?>
                    </div>
                </article>
            <?php endforeach?>

            <?php if (!$comments):?>
                <p class="p-4 text-secondary mb-0">暂无回复。</p>
            <?php endif?>
        </section>

        <section class="panel" id="Reply">
            <header class="panel-heading">
                <h2 class="panel-title">添加一条新回复</h2>
            </header>
            <div class="panel-body">
                <?php if (auth()->loggedIn()):?>
                    <form method="post" action="/topic/<?= (int) $topic['id'] ?>/comment">
                        <?= csrf_field() ?>
                        <textarea class="form-control js-editor" id="reply_body" name="body" required></textarea><button
                            class="btn btn-primary mt-3">提交</button>
                    </form>
                <?php else:?>
                    <div class="bg-light border rounded p-3 text-center"><a href="/reg">注册</a> 参与讨论 或 <a
                            href="/login">登录</a></div>
                <?php endif?>
            </div>
        </section>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?php if (auth()->loggedIn()):?>
    <script
        <?= csp_script_nonce() ?>>document.querySelectorAll('.js-reply-to').forEach(function(button){button.addEventListener('click',function(){const mention='@'+button.dataset.username+' ';const editor=window.tinymce&&tinymce.get('reply_body');if(editor){editor.focus();editor.insertContent(mention)}else{const field=document.getElementById('reply_body');field.value+=mention;field.focus()}location.hash='Reply'})});</script>
<?php endif?>

<?= $this->endSection() ?>
