<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading"><?= view('member/settings_nav', ['active' => 'password']) ?></div>
            <div class="panel-body">
                <form method="post" class="legacy-form-horizontal">
                    <?= csrf_field() ?>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="current_password">当前密码</label>
                        <div class="col-sm-10"><input id="current_password" class="form-control" type="password"
                                name="current_password" required></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label" for="password">新密码</label>
                        <div class="col-sm-10"><input id="password" class="form-control" type="password" name="password"
                                minlength="12" required></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-2 col-form-label"
                            for="password_confirmation">确认新密码</label>
                        <div class="col-sm-10"><input id="password_confirmation" class="form-control" type="password"
                                name="password_confirmation" minlength="12" required></div>
                    </div>
                    <div class="row">
                        <div class="offset-sm-2 col-sm-10"><button class="btn btn-primary" type="submit">修改密码</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
