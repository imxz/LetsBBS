<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <section class="col-md-9">
        <div class="panel">
            <div class="p-3 border-bottom d-flex justify-content-between">
                <div>
                    <h1 class="h5 mb-0"><?= esc($title) ?></h1>
                    <?php if ($currentNode):?>
                        <div class="meta mt-1"><?= esc($currentNode['description']) ?></div>
                    <?php endif?>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($nodeId && auth()->loggedIn()):?>
                        <form method="post" action="/node/<?= $nodeId ?>/follow">
                            <?= csrf_field() ?>
                            <button
                                class="btn btn-sm btn-outline-primary"><?= $nodeFollowing ? '取消关注' : '关注节点' ?></button>
                        </form>
                    <?php endif?>

                    <?php if (auth()->loggedIn()):?>
                        <a class="btn btn-sm btn-primary" href="/topic/new">发布主题</a>
                    <?php endif?>
                </div>
            </div>
            <?php if (!$topics):?>
                <p class="p-4 text-secondary mb-0">暂无主题。</p>
            <?php endif?>

            <?php foreach ($topics as $t):?>
                <article class="topic-row d-flex gap-3">
                    <div class="avatar">
                        <?php if ($t['avatar']):?>
                            <img class="avatar" src="<?= esc($t['avatar']) ?>" alt="">
                        <?php else:?>
                            <?= esc(strtoupper(substr($t['username'], 0, 1))) ?>

                        <?php endif?>
                    </div>
                    <div class="flex-grow-1"><a class="topic-title"
                            href="/topic/<?= $t['id'] ?>"><?= esc($t['title']) ?></a>
                        <div class="meta mt-1"><a href="/node/<?= $t['node_id'] ?>"><?= esc($t['node_name']) ?></a> · <a
                                href="/member/<?= esc($t['username'], 'url') ?>"><?= esc($t['username']) ?></a> ·
                            <?= esc($t['last_activity_at']) ?> · <?= (int) $t['comment_count'] ?> 回复</div>
                    </div>
                </article>
            <?php endforeach?>
        </div>
        <nav class="mt-3 d-flex justify-content-between">
            <?php if ($page > 1):?>
                <a class="btn btn-outline-secondary"
                    href="<?= $nodeId ? '/node/' . $nodeId . '/' . ($page - 1) : '/recent/' . ($page - 1) ?>">上一页</a>
            <?php else:?>
                <span></span>
            <?php endif?>

            <?php if ($hasNext):?>
                <a class="btn btn-outline-secondary"
                    href="<?= $nodeId ? '/node/' . $nodeId . '/' . ($page + 1) : '/recent/' . ($page + 1) ?>">下一页</a>
            <?php endif?>
        </nav>
    </section>
    <aside class="col-md-3 sidebar">
        <div class="panel p-3">
            <h2 class="h6">节点</h2>
            <div class="list-group list-group-flush">
                <?php foreach ($nodes as $n):?>
                    <a class="list-group-item list-group-item-action"
                        href="/node/<?= $n['id'] ?>"><?= esc($n['name']) ?><span
                            class="badge text-bg-light float-end"><?= (int) $n['topic_count'] ?></span></a>
                <?php endforeach?>
            </div>
        </div>
    </aside>
</div>
<?= $this->endSection() ?>
