<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">通知中心</h1>
                <div class="d-flex gap-3 mt-2"><a href="/settings/notifications">通知偏好</a>
                    <form method="post" action="/notification/read">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-link p-0">全部标为已读</button>
                    </form>
                </div>
            </div>
            <?php if (!$items):?>
                <p class="p-4 text-secondary">暂无通知。</p>
            <?php endif?>

            <?php foreach ($items as $n):?>
                <div class="topic-row <?= $n['read_at'] ? '' : 'bg-light' ?>">
                    <?php if ($n['actor_name']):?>
                        <a href="/member/<?= esc($n['actor_name'], 'url') ?>"><?= esc($n['actor_name']) ?></a>
                    <?php else:?>
                        某用户
                    <?php endif?>
                    <?php $noticeUrl = $n['comment_id'] && $n['comment_body'] ? reply_url((int) $n['topic_id'], (int) $n['comment_id']) : '/topic/' . $n['topic_id']; ?>

                    <?php if ($n['kind'] === 'moderation'): ?>
                        更新了你的内容状态，<a href="/my/content">查看审核结果</a>
                    <?php elseif ($n['kind'] === 'follow'):?>
                        关注了你
                    <?php elseif ($n['title'] === null):?>
                        的相关主题已不可用
                    <?php elseif ($n['kind'] === 'topic'):?>
                        发布了主题 <a href="<?= esc($noticeUrl) ?>"><?= esc($n['title']) ?></a>
                    <?php elseif ($n['kind'] === 'mention'):?>
                        在主题 <a href="<?= esc($noticeUrl) ?>"><?= esc($n['title']) ?></a> 中提到了你
                    <?php else:?>
                        回复了主题 <a href="<?= esc($noticeUrl) ?>"><?= esc($n['title']) ?></a>
                    <?php endif?>

                    <?php if (!empty($n['comment_body'])):?>
                        <blockquote class="post-body border-start ps-3 mt-2 mb-1"><?= $n['comment_body'] ?></blockquote>
                    <?php endif?>
                    <div class="meta"><?= esc(forum_date($n['created_at'])) ?></div>
                </div>
            <?php endforeach?>
        </div>
        <nav class="mt-3 d-flex justify-content-between">
            <?php if ($page > 1):?>
                <a class="btn btn-outline-secondary" href="/notification/<?= $page - 1 ?>">上一页</a>
            <?php else:?>
                <span></span>
            <?php endif?>

            <?php if ($hasNext):?>
                <a class="btn btn-outline-secondary" href="/notification/<?= $page + 1 ?>">下一页</a>
            <?php endif?>
        </nav>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
