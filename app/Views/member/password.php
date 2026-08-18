<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="panel p-4">
            <?= view('member/settings_nav', ['active' => 'password']) ?>
            <h1 class="h4">修改密码</h1>
            <form method="post">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label">当前密码</label><input class="form-control" type="password"
                        name="current_password" required></div>
                <div class="mb-3"><label class="form-label">新密码</label><input class="form-control" type="password"
                        name="password" minlength="12" required></div>
                <div class="mb-3"><label class="form-label">确认新密码</label><input class="form-control" type="password"
                        name="password_confirmation" minlength="12" required></div><button class="btn btn-primary"
                    type="submit">修改密码</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
