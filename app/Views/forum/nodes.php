<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <header class="panel-heading">
                <h1 class="panel-title"><a href="/">首页</a> / 所有节点</h1>
            </header>
            <div class="panel-body node-directory">
                <?= view('forum/_node_directory', ['directoryNodes' => $nodes]) ?>

                <?php if (!$nodes):?>
                    <p class="text-secondary mb-0">暂无节点。</p>
                <?php endif?>
            </div>
        </div>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
