<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8 offset-lg-2">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">请检查邮箱</h1>
            </div>
            <div class="panel-body">
                <p class="mb-0">如果邮箱与账号匹配，一次性登录链接已经发出。请同时检查垃圾邮件目录。</p>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
