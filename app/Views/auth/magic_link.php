<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8 offset-lg-2">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">通过邮箱找回账号</h1>
            </div>
            <div class="panel-body">
                <?php if (session('errors')):?>
                    <div class="alert alert-danger"><?= esc(implode('；', session('errors'))) ?></div>
                <?php endif?>
                <p class="text-secondary">填写注册邮箱，我们会发送一条一小时内有效的一次性登录链接。</p>
                <form method="post" action="<?= url_to('magic-link') ?>" class="legacy-form-horizontal">
                    <?= csrf_field() ?>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="email">邮箱</label>
                        <div class="col-sm-10"><input id="email" class="form-control" type="email" name="email"
                                maxlength="254" autocomplete="email" value="<?= esc(old('email')) ?>" required></div>
                    </div>
                    <div class="row">
                        <div class="offset-sm-2 col-sm-10"><button class="btn btn-primary" type="submit">发送登录链接</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
