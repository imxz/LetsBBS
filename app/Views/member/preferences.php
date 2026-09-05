<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading"><?= view('member/settings_nav', ['active' => 'notifications']) ?></div>
            <div class="panel-body">
                <h1 class="h5">通知偏好</h1>
                <p>主题关注同时用于收藏和订阅回复。下面的开关只影响今后收到的站内通知，不会取消收藏。</p>
                <form method="post">
                    <?= csrf_field() ?>

                    <?php foreach (['notify_topics' => '关注的节点或用户发布新主题', 'notify_comments' => '我发表或关注的主题有新回复', 'notify_mentions' => '有人在回复中 @ 我', 'notify_follows' => '有人关注我'] as $key => $label): ?>
                        <label class="form-check my-3"><input class="form-check-input" type="checkbox"
                                name="<?= $key ?>" value="1"
                                <?= $profile[$key] ? 'checked' : '' ?>><?= $label ?></label>
                    <?php endforeach ?>
                    <p class="meta">内容审核结果始终通知。无需配置 SMTP。</p><button class="btn btn-primary">保存</button>
                </form>
            </div>
        </div>
    </section><?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
