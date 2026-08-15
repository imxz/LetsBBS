<?= $this->extend('layouts/main') ?>

<?= $this->setData(['editor' => true]) ?>

<?= $this->section('content') ?>
<div class="panel p-4">
    <h1 class="h4">发布主题</h1>
    <form method="post" action="/topic">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label">节点</label><select class="form-select" name="node_id" required>
                <?php foreach ($nodes as $n):?>
                    <option value="<?= $n['id'] ?>"><?= esc($n['name']) ?></option>
                <?php endforeach?>
            </select></div>
        <div class="mb-3"><label class="form-label">标题</label><input class="form-control" name="title" maxlength="160"
                value="<?= esc(old('title')) ?>" required></div>
        <div class="mb-3"><label class="form-label">正文</label><textarea class="form-control js-editor" name="body"
                required><?= esc(old('body')) ?></textarea></div><button class="btn btn-primary">发布</button>
    </form>
</div>
<?= $this->endSection() ?>
