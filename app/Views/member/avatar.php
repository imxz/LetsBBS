<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="panel p-4">
            <?= view('member/settings_nav', ['active' => 'avatar']) ?>
            <h1 class="h4">上传头像</h1>
            <?php if (!empty($profile['avatar'])):?>
                <div class="mb-3"><img class="profile-avatar-preview" src="<?= esc($profile['avatar']) ?>" alt="当前头像">
                </div>
            <?php endif?>
            <form method="post" action="/settings/avatar" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <label class="form-label" for="avatar">选择新头像</label><input id="avatar" class="form-control" type="file"
                    name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" required>
                <div class="form-text">支持 JPG、PNG、GIF、WebP，最大 2 MiB，尺寸不超过 512×512。</div><button
                    class="btn btn-primary mt-3" type="submit">上传</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
