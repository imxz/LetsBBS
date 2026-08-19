<?php helper('forum'); ?>
<?php $homeTopNodes = array_values(array_filter($nodes, static fn(array $node): bool => (bool) ($node['show_on_home'] ?? false)));
if (!$homeTopNodes) {
    $homeTopNodes = array_slice($nodes, 0, 6);
} ?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="p-3 border-bottom d-flex justify-content-between gap-3">
                <div>
                    <h1 class="h5 mb-0"><?= esc($title) ?></h1>
                    <?php if ($currentNode):?>
                        <div class="meta mt-1"><?= esc($currentNode['description']) ?></div>
                    <?php endif?>
                </div>
                <div class="d-flex gap-2 align-items-start">
                    <?php if ($nodeId && auth()->loggedIn()):?>
                        <form method="post" action="/node/<?= $nodeId ?>/follow">
                            <?= csrf_field() ?>
                            <button
                                class="btn btn-sm btn-outline-primary"><?= $nodeFollowing ? '取消关注' : '关注节点' ?></button>
                        </form>
                    <?php endif?>
                    <a class="btn btn-sm btn-primary" href="/topic/new">发布主题</a>
                </div>
            </div>
            <?php if ($home):?>
                <nav class="topic-filters p-3 border-bottom d-flex flex-wrap gap-2" aria-label="主题筛选">
                    <?php foreach (array_slice($homeTopNodes, 0, 10) as $node):?>
                        <a class="btn btn-sm btn-outline-secondary"
                            href="/node/<?= $node['id'] ?>"><?= esc($node['name']) ?></a>
                    <?php endforeach?>
                    <a class="btn btn-sm <?= $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' ?>"
                        href="/?filter=all">全部</a>
                    <?php if (auth()->loggedIn()):?>
                        <a class="btn btn-sm <?= $filter === 'nodes' ? 'btn-primary' : 'btn-outline-secondary' ?>"
                            href="/?filter=nodes">节点收藏</a>
                        <a class="btn btn-sm <?= $filter === 'topics' ? 'btn-primary' : 'btn-outline-secondary' ?>"
                            href="/?filter=topics">主题收藏</a>
                        <a class="btn btn-sm <?= $filter === 'users' ? 'btn-primary' : 'btn-outline-secondary' ?>"
                            href="/?filter=users">特别关注</a>
                    <?php endif?>
                </nav>
            <?php endif?>

            <?php if (!$topics):?>
                <p class="p-4 text-secondary mb-0"><?= $search !== '' ? '没有找到相关主题。' : '暂无主题。' ?></p>
            <?php endif?>

            <?php foreach ($topics as $topic):?>
                <article class="topic-row d-flex gap-3 align-items-start">
                    <a class="avatar flex-shrink-0" href="/member/<?= esc($topic['username'], 'url') ?>"
                        aria-label="<?= esc($topic['username']) ?> 的主页">
                        <?php if ($topic['avatar']):?>
                            <img class="avatar" src="<?= esc($topic['avatar']) ?>"
                                alt="<?= esc($topic['username']) ?> 的头像">
                        <?php else:?>
                            <?= esc(strtoupper(substr($topic['username'], 0, 1))) ?>

                        <?php endif?>
                    </a>
                    <div class="flex-grow-1 min-w-0">
                        <a class="topic-title" href="/topic/<?= $topic['id'] ?>"><?= esc($topic['title']) ?></a>
                        <div class="meta mt-1">
                            <a href="/node/<?= $topic['node_id'] ?>"><?= esc($topic['node_name']) ?></a> ·
                            <a href="/member/<?= esc($topic['username'], 'url') ?>"><?= esc($topic['username']) ?></a> ·
                            <?= esc(relative_time($topic['last_activity_at'])) ?> ·
                            <?php if ($topic['last_reply_username']):?>
                                最后回复来自 <a
                                    href="/member/<?= esc($topic['last_reply_username'], 'url') ?>"><?= esc($topic['last_reply_username']) ?></a>
                            <?php else:?>
                                暂无回复
                            <?php endif?>
                        </div>
                    </div>
                    <a class="reply-count flex-shrink-0"
                        href="/topic/<?= $topic['id'] ?><?= $topic['comment_count'] ? '#reply-' . (int) $topic['comment_count'] : '' ?>"
                        aria-label="<?= (int) $topic['comment_count'] ?> 条回复"><?= (int) $topic['comment_count'] ?></a>
                </article>
            <?php endforeach?>
        </div>

        <nav class="mt-3 d-flex justify-content-between" aria-label="主题分页">
            <?php if ($previousUrl):?>
                <a class="btn btn-outline-secondary" href="<?= esc($previousUrl) ?>">上一页</a>
            <?php else:?>
                <span></span>
            <?php endif?>

            <?php if ($nextUrl):?>
                <a class="btn btn-outline-secondary" href="<?= esc($nextUrl) ?>">下一页</a>
            <?php endif?>
        </nav>
        <?php if ($home):?>
            <section class="panel p-3 mt-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h2 class="h6 mb-0">节点导航</h2>
                    <a href="/node">浏览所有节点</a>
                </div>
                <?php $featuredNodes = array_values(array_filter($nodes, static fn(array $node): bool => (bool) ($node['featured'] ?? true)));
            $children = [];
            $roots = [];
            foreach ($featuredNodes as $node) {
                if ($node['parent_id']) {
                    $children[(int) $node['parent_id']][] = $node;
                } else {
                    $roots[] = $node;
                }
            } ?>

                <?php foreach ($roots as $root):?>

                    <?php if (!empty($children[(int) $root['id']])):?>
                        <div class="row mb-2">
                            <div class="col-sm-3 meta pt-1"><?= esc($root['name']) ?></div>
                            <div class="col-sm-9 d-flex flex-wrap gap-2">
                                <?php foreach ($children[(int) $root['id']] as $child):?>
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="/node/<?= (int) $child['id'] ?>"><?= esc($child['name']) ?> <span
                                            class="badge text-bg-light ms-1"><?= (int) $child['topic_count'] ?></span></a>
                                <?php endforeach?>
                            </div>
                        </div>
                    <?php else:?>
                        <a class="btn btn-sm btn-outline-secondary me-1 mb-2"
                            href="/node/<?= (int) $root['id'] ?>"><?= esc($root['name']) ?> <span
                                class="badge text-bg-light ms-1"><?= (int) $root['topic_count'] ?></span></a>
                    <?php endif?>

                <?php endforeach?>
            </section>
        <?php endif?>
    </section>

    <aside class="col-lg-4 sidebar">
        <?php if ($home):?>
            <section class="panel p-3 mb-3">
                <h2 class="h6">你好<?= $viewer ? '，' . esc($viewer['username']) : '' ?></h2>
                <?php if ($viewer):?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <a class="avatar" href="/member/<?= esc($viewer['username'], 'url') ?>">
                            <?php if ($viewer['avatar']):?>
                                <img class="avatar" src="<?= esc($viewer['avatar']) ?>"
                                    alt="<?= esc($viewer['username']) ?> 的头像">
                            <?php else:?>
                                <?= esc(strtoupper(substr($viewer['username'], 0, 1))) ?>

                            <?php endif?>
                        </a>
                        <a href="/member/<?= esc($viewer['username'], 'url') ?>"><?= esc($viewer['username']) ?></a>
                    </div>
                    <div class="row g-0 text-center follow-summary">
                        <a class="col"
                            href="/?filter=nodes"><strong><?= (int) $viewer['node_follows'] ?></strong><span>节点收藏</span></a>
                        <a class="col"
                            href="/?filter=topics"><strong><?= (int) $viewer['topic_follows'] ?></strong><span>主题收藏</span></a>
                        <a class="col"
                            href="/?filter=users"><strong><?= (int) $viewer['user_follows'] ?></strong><span>特别关注</span></a>
                    </div>
                    <a class="d-block border-top mt-3 pt-3"
                        href="/notification"><?= (int) $viewer['unread_notifications'] ?> 条未读提醒</a>
                <?php else:?>
                    <p><?= nl2br(esc($siteIntroduction)) ?></p>
                    <div class="d-flex gap-2"><a class="btn btn-sm btn-primary" href="/register">注册</a><a
                            class="btn btn-sm btn-outline-secondary" href="/login">登录</a></div>
                <?php endif?>
            </section>

            <section class="panel mb-3">
                <h2 class="h6 p-3 mb-0 border-bottom">热门主题</h2>
                <?php if (!$hotTopics):?>
                    <p class="p-3 text-secondary mb-0">暂无热门主题。</p>
                <?php endif?>
                <div class="list-group list-group-flush">
                    <?php foreach ($hotTopics as $topic):?>
                        <a class="list-group-item list-group-item-action d-flex justify-content-between gap-3"
                            href="/topic/<?= $topic['id'] ?>"><span><?= esc($topic['title']) ?></span><span
                                class="badge text-bg-light"><?= (int) $topic['comment_count'] ?></span></a>
                    <?php endforeach?>
                </div>
            </section>

            <section class="panel p-3">
                <h2 class="h6">社区运行状况</h2>
                <dl class="row mb-0 community-stats">
                    <dt class="col-7">注册会员</dt>
                    <dd class="col-5 text-end"><?= (int) $statistics['users'] ?></dd>
                    <dt class="col-7">主题</dt>
                    <dd class="col-5 text-end"><?= (int) $statistics['topics'] ?></dd>
                    <dt class="col-7">回复</dt>
                    <dd class="col-5 text-end mb-0"><?= (int) $statistics['comments'] ?></dd>
                </dl>
            </section>
        <?php else:?>
            <section class="panel p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">节点</h2>
                    <a class="small" href="/node">全部</a>
                </div>
                <div class="list-group list-group-flush mt-2">
                    <?php foreach ($nodes as $node):?>
                        <a class="list-group-item list-group-item-action"
                            href="/node/<?= $node['id'] ?>"><?= esc($node['name']) ?><span
                                class="badge text-bg-light float-end"><?= (int) $node['topic_count'] ?></span></a>
                    <?php endforeach?>
                </div>
            </section>
        <?php endif?>
    </aside>
</div>
<?= $this->endSection() ?>
