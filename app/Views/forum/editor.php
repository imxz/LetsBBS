<?= $this->extend('layouts/main') ?>

<?php $this->setData(['editor' => true]) ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">创建新主题</h1>
            </div>
            <div class="panel-body">
                <form method="post" action="/topic">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label" for="title">标题</label><input id="title"
                            class="form-control" name="title" maxlength="160" placeholder="标题"
                            value="<?= esc(old('title')) ?>" required></div>
                    <div class="mb-3"><label class="form-label" for="node_id">节点</label><select id="node_id"
                            class="form-select" name="node_id" required>
                            <option value="">请选择分类</option>
                            <?php foreach ($nodes as $n):?>
                                <option value="<?= $n['id'] ?>"><?= esc($n['name']) ?></option>
                            <?php endforeach?>
                        </select></div>
                    <div class="mb-3"><label class="form-label visually-hidden" for="body">正文</label><textarea id="body"
                            class="form-control js-editor" name="body" required><?= esc(old('body')) ?></textarea></div>
                    <button class="btn btn-primary">提交</button>
                </form>
            </div>
        </div>
    </section>
    <?= $this->include('forum/_common_sidebar') ?>
</div>
<?= $this->endSection() ?>
