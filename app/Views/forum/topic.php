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
                                href="/node/<?= esc($topic['node_slug'], 'url') ?>"><?= esc($topic['node_name']) ?></a>
                        </div>
                        <h1 class="topic-detail-title"><?= esc($topic['title']) ?></h1>
                        <?php if ($preview):?>
                            <div class="mt-2"><span
                                    class="badge text-bg-warning">管理员预览：<?= content_status($topic['status']) ?>，或节点已隐藏</span>
                            </div>
                        <?php endif?>
                        <div class="meta">By <a
                                href="/member/<?= esc($topic['username'], 'url') ?>"><?= esc($topic['username']) ?></a>
                            · <?= esc(forum_date($topic['created_at'])) ?> · <?= (int) $topic['view_count'] ?> 次点击
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
            <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')): ?>
                <div class="panel-body border-top">
                    <form class="d-flex flex-wrap gap-3" method="post"
                        action="/admin/topic/<?= (int) $topic['id'] ?>/flags">
                        <?= csrf_field() ?>
                        <label><input type="checkbox" name="is_pinned" value="1"
                                <?= $topic['is_pinned'] ? 'checked' : '' ?>> 置顶</label><label><input type="checkbox"
                                name="is_locked" value="1" <?= $topic['is_locked'] ? 'checked' : '' ?>>
                            锁定回复</label><button class="btn btn-sm btn-outline-secondary">保存</button>
                    </form><?= view('admin/_moderate', ['row' => $topic, 'resource' => 'topic']) ?>
                </div>
            <?php endif ?>
            <footer class="panel-footer d-flex justify-content-between align-items-center">
                <?php if (!$preview && auth()->loggedIn()):?>
                    <form method="post" action="/topic/<?= (int) $topic['id'] ?>/follow">
                        <?= csrf_field() ?>
                        <button
                            class="btn btn-sm btn-link text-secondary p-0"><?= $following ? '取消关注' : '关注主题' ?></button>
                    </form>
                <?php elseif (!$preview):?>
                    <a class="small text-secondary" href="/login">登录后关注主题</a>
                <?php endif?>

                <?php if (auth()->loggedIn() && (auth()->id() == $topic['user_id'] || auth()->user()->inGroup('admin'))):?>
                    <form method="post" action="/topic/<?= (int) $topic['id'] ?>/delete">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-link text-danger p-0" data-confirm="确认将主题移入回收站？">删除</button>
                    </form>
                <?php endif?>
            </footer>
        </article>

        <section class="panel mb-3">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title"><small><?= (int) $topic['comment_count'] ?> 回复 · 截至现在</small></h2>
                <?php if ($canReply):?>
                    <a class="small text-secondary" href="#Reply">添加回复</a>
                <?php endif?>
            </header>
            <?php foreach ($comments as $i => $comment):?>
                <article id="comment-<?= (int) $comment['id'] ?>" class="topic-row d-flex gap-3"><span
                        id="reply-<?= ($page - 1) * 30 + $i + 1 ?>"></span><span
                        id="Reply<?= ($page - 1) * 30 + $i + 1 ?>"></span>
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
                                data-username="<?= esc($comment['username']) ?>">#<?= ($page - 1) * 30 + $i + 1 ?>
                                ↩</button>
                        </div>
                        <div class="post-body mt-2"><?= $comment['body'] ?></div>
                        <a class="small"
                            href="<?= esc(reply_url((int) $topic['id'], (int) $comment['id'])) ?>">此回复链接</a>
                        <?php if (!$preview && auth()->loggedIn()): ?>
                            <?= view('forum/_report', ['topicId' => $topic['id'], 'commentId' => $comment['id']]) ?>

                        <?php endif ?>

                        <?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')):?>
                            <form class="mt-2" method="post"
                                action="/admin/comment/<?= (int) $comment['id'] ?>/moderate">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-link text-warning p-0" name="status"
                                    value="hidden">隐藏回复</button>
                                <button class="btn btn-sm btn-link text-danger p-0" name="status" value="deleted"
                                    data-confirm="确认将回复移入回收站？">删除回复</button>
                            </form>
                        <?php endif?>
                    </div>
                </article>
            <?php endforeach?>

            <?php if (!$comments):?>
                <p class="p-4 text-secondary mb-0">暂无回复。</p>
            <?php endif?>
        </section>
        <nav class="d-flex justify-content-between mb-3">
            <?php if ($page > 1): ?>
                <a href="/topic/<?= (int) $topic['id'] ?>?page=<?= $page - 1 ?>">上一页回复</a>
            <?php endif ?>
            <span>第 <?= $page ?> 页</span>
            <?php if ($hasNext): ?>
                <a href="/topic/<?= (int) $topic['id'] ?>?page=<?= $page + 1 ?>">下一页回复</a>
            <?php endif ?>
        </nav>
        <?php if (!$preview && auth()->loggedIn()): ?>
            <?= view('forum/_report', ['topicId' => $topic['id'], 'commentId' => null]) ?>

        <?php endif ?>

        <?php if ($canReply):?>
            <section class="panel" id="Reply">
                <header class="panel-heading">
                    <h2 class="panel-title">添加一条新回复</h2>
                </header>
                <div class="panel-body">
                    <?php if (auth()->loggedIn()):?>
                        <form method="post" action="/topic/<?= (int) $topic['id'] ?>/comment"
                            data-draft-key="reply-<?= (int) $topic['id'] ?>">
                            <?= csrf_field() ?>
                            <textarea class="form-control js-editor" id="reply_body" name="body"
                                required><?= esc(old('body')) ?></textarea><button
                                class="btn btn-primary mt-3">提交</button>
                        </form>
                    <?php else:?>
                        <div class="bg-light border rounded p-3 text-center"><a href="/reg">注册</a> 参与讨论 或 <a
                                href="/login">登录</a></div>
                    <?php endif?>
                </div>
            </section>
        <?php else: ?>
            <p class="alert alert-secondary">此主题当前不接受新回复。</p>
        <?php endif?>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?php if ($canReply && auth()->loggedIn()):?>
    <script
        <?= csp_script_nonce() ?>>document.querySelectorAll('.js-reply-to').forEach(function(button){button.addEventListener('click',function(){const mention='@'+button.dataset.username+' ';const editor=window.tinymce&&tinymce.get('reply_body');if(editor){editor.focus();editor.insertContent(mention)}else{const field=document.getElementById('reply_body');field.value+=mention;field.focus()}location.hash='Reply'})});</script>
<?php endif?>

<?= $this->endSection() ?>
