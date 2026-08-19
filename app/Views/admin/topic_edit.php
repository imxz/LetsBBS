<?= $this->extend('layouts/main') ?>

<?php $this->setData(['editor' => true]) ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel p-4">
            <h1 class="h5 border-bottom pb-3 mb-3">编辑主题</h1>
            <form method="post" action="/admin/topic/<?= (int) $topic['id'] ?>/edit">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label" for="title">标题</label><input class="form-control" id="title"
                        name="title" maxlength="160" value="<?= esc(old('title', $topic['title'])) ?>" required></div>
                <div class="mb-3"><label class="form-label" for="node_id">节点</label><select class="form-select"
                        id="node_id" name="node_id" required>
                        <?php foreach ($nodes as $node):?>
                            <option value="<?= (int) $node['id'] ?>"
                                <?= (int) old('node_id', $topic['node_id']) === (int) $node['id'] ? 'selected' : '' ?>>
                                <?= !empty($node['parent_id']) ? '　' : '' ?><?= esc($node['name']) ?></option>
                        <?php endforeach?>
                    </select></div>
                <div class="mb-3"><label class="form-label" for="body">正文</label><textarea
                        class="form-control js-editor" id="body" name="body"
                        required><?= esc(old('body', $topic['body'])) ?></textarea></div>
                <button class="btn btn-primary">提交</button>
            </form>
        </div>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
