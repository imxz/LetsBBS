<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading"><?= view('member/settings_nav', ['active' => 'avatar']) ?></div>
            <div class="panel-body">
                <form method="post" action="/settings/avatar" enctype="multipart/form-data"
                    class="legacy-form-horizontal">
                    <?= csrf_field() ?>

                    <?php if (!empty($profile['avatar'])):?>
                        <div class="row form-row"><label class="col-sm-2 col-form-label">当前头像</label>
                            <div class="col-sm-10"><img class="profile-avatar-preview"
                                    src="<?= esc($profile['avatar']) ?>" alt="当前头像"></div>
                        </div>
                    <?php endif?>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="avatar">新头像</label>
                        <div class="col-sm-10"><input id="avatar" class="form-control" type="file" name="avatar"
                                accept="image/jpeg,image/png,image/gif,image/webp" required>
                            <div class="alert alert-info avatar-help"><strong>注意</strong> 支持 JPG、PNG、GIF、WebP，最大 2
                                MiB，尺寸不超过 512×512，推荐使用正方形图片。</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-sm-2 col-sm-10"><button class="btn btn-primary" type="submit">上传</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
