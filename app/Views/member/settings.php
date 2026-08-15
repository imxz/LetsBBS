<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-3">
    <div class="col-md-7">
        <div class="panel p-4">
            <h1 class="h4">个人设置</h1>
            <form method="post">
                <?= csrf_field() ?>
                <label class="form-label">个人简介</label><textarea class="form-control" name="bio" maxlength="500"
                    rows="6"><?= esc($profile['bio'] ?? '') ?></textarea><button
                    class="btn btn-primary mt-3">保存</button>
            </form>
        </div>
    </div>
    <div class="col-md-5">
        <div class="panel p-4">
            <h2 class="h5">头像</h2>
            <form method="post" action="/settings/avatar" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input class="form-control" type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp"
                    required>
                <div class="form-text">最大 2 MiB，尺寸不超过 512×512。</div><button
                    class="btn btn-outline-primary mt-3">上传头像</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
