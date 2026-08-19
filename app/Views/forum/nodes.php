<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $children = [];
$roots = [];
foreach ($nodes as $node) {
    if ($node['parent_id']) {
        $children[(int) $node['parent_id']][] = $node;
    } else {
        $roots[] = $node;
    }
} ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <header class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h1 class="h5 mb-0"><a href="/">首页</a> / 所有节点</h1><a class="btn btn-sm btn-primary"
                    href="/topic/new">发布主题</a>
            </header>
            <div class="p-4">
                <?php $standalone = [];
foreach ($roots as $root):?>

                <?php if (!empty($children[(int) $root['id']])):?>
                    <div class="row mb-3">
                        <div class="col-sm-3 fw-semibold"><?= esc($root['name']) ?></div>
                        <div class="col-sm-9 d-flex flex-wrap gap-2">
                            <?php foreach ($children[(int) $root['id']] as $child):?>
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="/node/<?= (int) $child['id'] ?>"><?= esc($child['name']) ?> <span
                                        class="badge text-bg-light"><?= (int) $child['topic_count'] ?></span></a>
                            <?php endforeach?>
                        </div>
                    </div>
                <?php else:?>
                    <?php $standalone[] = $root; ?>

                <?php endif?>

                <?php endforeach?>

                <?php if ($standalone):?>
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-9 d-flex flex-wrap gap-2">
                            <?php foreach ($standalone as $node):?>
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="/node/<?= (int) $node['id'] ?>"><?= esc($node['name']) ?> <span
                                        class="badge text-bg-light"><?= (int) $node['topic_count'] ?></span></a>
                            <?php endforeach?>
                        </div>
                    </div>
                <?php endif?>

                <?php if (!$nodes):?>
                    <p class="text-secondary mb-0">暂无节点。</p>
                <?php endif?>
            </div>
        </div>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
