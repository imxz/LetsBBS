<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="panel">
    <div class="p-3 border-bottom d-flex justify-content-between">
        <h1 class="h5 mb-0">通知</h1>
        <form method="post" action="/notification/read">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-secondary">全部标为已读</button>
        </form>
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

            <?php if ($n['kind'] === 'follow'):?>
                关注了你
            <?php elseif ($n['title'] === null):?>
                的相关主题已不可用
            <?php elseif ($n['kind'] === 'topic'):?>
                发布了主题 <a href="/topic/<?= (int) $n['topic_id'] ?>"><?= esc($n['title']) ?></a>
            <?php else:?>
                回复了主题 <a href="/topic/<?= (int) $n['topic_id'] ?>"><?= esc($n['title']) ?></a>
            <?php endif?>
            <div class="meta"><?= esc($n['created_at']) ?></div>
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
<?= $this->endSection() ?>
