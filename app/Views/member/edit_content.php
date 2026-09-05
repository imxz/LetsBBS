<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">修改我的内容</h1>
            </div>
            <div class="panel-body">
                <p class="meta">发表后 30 分钟内可修改。启用审核时，修改后的内容需重新审核。</p>
                <form method="post" data-draft-key="edit-<?= $kind ?>-<?= (int) $row['id'] ?>">
                    <?= csrf_field() ?>

                    <?php if ($kind === 'topics'): ?>
                        <label class="form-label" for="title">标题</label><input class="form-control mb-3" id="title"
                            name="title" minlength="2" maxlength="160" value="<?= esc(old('title', $row['title'])) ?>"
                            required>
                    <?php endif ?>
                    <textarea class="form-control js-editor" id="edit-body" name="body"
                        required><?= esc(old('body', $row['body'])) ?></textarea><button
                        class="btn btn-primary mt-3">保存修改</button>
                </form>
            </div>
        </div>
    </section><?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
