<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <section class="col-md-8">
        <div class="panel p-4 mb-3">
            <div class="d-flex gap-3">
                <div class="avatar">
                    <?php if ($member['avatar']):?>
                        <img class="avatar" src="<?= esc($member['avatar']) ?>" alt="">
                    <?php else:?>
                        <?= esc(strtoupper(substr($member['username'], 0, 1))) ?>

                    <?php endif?>
                </div>
                <div>
                    <h1 class="h4 mb-1"><?= esc($member['username']) ?></h1>
                    <div class="meta">第 <?= (int) $member['id'] ?> 号会员 · 加入于 <?= esc($member['created_at']) ?></div>
                </div>
            </div>
            <?php if (!empty($member['signature'])):?>
                <p class="mt-3 mb-2">签名：<?= esc($member['signature']) ?></p>
            <?php endif?>

            <?php if (!empty($member['homepage'])):?>
                <p class="mb-2">个人主页：<a href="<?= esc($member['homepage']) ?>" rel="nofollow noopener"
                        target="_blank"><?= esc($member['homepage']) ?></a></p>
            <?php endif?>

            <?php if (!empty($member['location'])):?>
                <p class="mb-2">所在地：<?= esc($member['location']) ?></p>
            <?php endif?>
            <p class="mt-3 mb-2"><?= nl2br(esc($member['bio'] ?? '')) ?></p>
            <div class="meta"><?= (int) ($member['topic_count'] ?? 0) ?> 主题 ·
                <?= (int) ($member['comment_count'] ?? 0) ?> 回复 · <?= (int) ($member['follower_count'] ?? 0) ?> 关注者
            </div>
            <?php if (auth()->loggedIn() && auth()->id() != $member['id']):?>
                <form class="mt-3" method="post" action="/member/<?= esc($member['username'], 'url') ?>/follow">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-primary"
                        type="submit"><?= $following ? '取消关注' : '关注用户' ?></button>
                </form>
            <?php endif?>
        </div>
        <div class="panel">
            <div class="p-3 border-bottom"><strong>最近主题</strong></div>
            <?php if (!$topics):?>
                <p class="p-3 mb-0 text-secondary">暂无主题。</p>
            <?php endif?>

            <?php foreach ($topics as $t):?>
                <div class="topic-row"><a href="/topic/<?= $t['id'] ?>"><?= esc($t['title']) ?></a>
                    <div class="meta"><?= esc($t['created_at']) ?></div>
                </div>
            <?php endforeach?>
        </div>
        <div class="panel mt-3">
            <div class="p-3 border-bottom"><strong>最近回复</strong></div>
            <?php if (!$comments):?>
                <p class="p-3 mb-0 text-secondary">暂无回复。</p>
            <?php endif?>

            <?php foreach ($comments as $comment):?>
                <article class="topic-row">
                    <div class="mb-2">回复了 <?= esc($comment['topic_author']) ?> 创建的主题：<a
                            href="/topic/<?= $comment['topic_id'] ?>"><?= esc($comment['title']) ?></a>
                    </div>
                    <blockquote class="mb-1 post-body"><?= $comment['body'] ?></blockquote>
                    <div class="meta"><?= esc($comment['created_at']) ?></div>
                </article>
            <?php endforeach?>
        </div>
    </section>
    <aside class="col-md-4 sidebar">
        <div class="panel p-3"><a href="/member/<?= esc($member['username'], 'url') ?>/topics/1">全部主题</a><br><a
                href="/member/<?= esc($member['username'], 'url') ?>/comments/1">全部回复</a>
            <?php if (auth()->loggedIn() && auth()->id() == $member['id']):?>
                <hr><a href="/settings">个人设置</a>
            <?php endif?>
        </div>
    </aside>
</div>
<?= $this->endSection() ?>
