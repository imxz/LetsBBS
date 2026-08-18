<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="panel p-4">
    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
        <div>
            <h1 class="h4 mb-1">全部节点</h1>
            <div class="meta">浏览社区的全部讨论分区</div>
        </div>
        <a class="btn btn-sm btn-primary" href="/topic/new">发布主题</a>
    </div>
    <div class="row g-3">
        <?php foreach ($nodes as $node):?>
            <div class="col-sm-6 col-lg-4">
                <a class="node-card d-block h-100" href="/node/<?= $node['id'] ?>">
                    <div class="d-flex justify-content-between gap-3">
                        <strong><?= esc($node['name']) ?></strong>
                        <span class="badge text-bg-light"><?= (int) $node['topic_count'] ?></span>
                    </div>
                    <div class="meta mt-2"><?= esc($node['description']) ?></div>
                </a>
            </div>
        <?php endforeach?>
    </div>
</section>
<?= $this->endSection() ?>
