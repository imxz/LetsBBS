<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $isEdit = $node !== null; ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel p-4">
            <h1 class="h5 border-bottom pb-3 mb-3"><?= $isEdit ? '编辑节点' : '添加节点' ?></h1>
            <form method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="name">节点名称</label><input class="form-control"
                            id="name" name="name" maxlength="80" value="<?= esc(old('name', $node['name'] ?? '')) ?>"
                            required></div>
                    <div class="col-md-6"><label class="form-label" for="slug">Slug</label><input class="form-control"
                            id="slug" name="slug" pattern="[a-z0-9-]{2,80}"
                            value="<?= esc(old('slug', $node['slug'] ?? '')) ?>" required></div>
                    <div class="col-md-6"><label class="form-label" for="parent_id">父节点</label><select
                            class="form-select" id="parent_id" name="parent_id">
                            <option value="0">无</option>
                            <?php foreach ($parents as $parent):?>
                                <option value="<?= (int) $parent['id'] ?>"
                                    <?= (int) old('parent_id', $node['parent_id'] ?? 0) === (int) $parent['id'] ? 'selected' : '' ?>>
                                    <?= esc($parent['name']) ?></option>
                            <?php endforeach?>
                        </select></div>
                    <div class="col-md-6"><label class="form-label" for="sort_order">排序</label><input
                            class="form-control" type="number" id="sort_order" name="sort_order"
                            value="<?= (int) old('sort_order', $node['sort_order'] ?? 0) ?>"></div>
                    <div class="col-12"><label class="form-label" for="keywords">关键字</label><input class="form-control"
                            id="keywords" name="keywords" maxlength="160"
                            value="<?= esc(old('keywords', $node['keywords'] ?? '')) ?>"></div>
                    <div class="col-12"><label class="form-label" for="description">简介</label><textarea
                            class="form-control" id="description" name="description" maxlength="255"
                            rows="3"><?= esc(old('description', $node['description'] ?? '')) ?></textarea></div>
                    <?php foreach ([['featured', '首页节点导航', 1], ['show_on_home', '首页置顶按钮', 0], ['is_active', '节点状态', 1]] as [$key, $label, $default]):?>
                        <div class="col-md-4"><label class="form-label" for="<?= $key ?>"><?= $label ?></label><select
                                class="form-select" id="<?= $key ?>" name="<?= $key ?>">
                                <option value="1"
                                    <?= (int) old($key, $node[$key] ?? $default) === 1 ? 'selected' : '' ?>>
                                    <?= $key === 'is_active' ? '启用' : '显示' ?></option>
                                <option value="0"
                                    <?= (int) old($key, $node[$key] ?? $default) === 0 ? 'selected' : '' ?>>
                                    <?= $key === 'is_active' ? '停用' : '不显示' ?></option>
                            </select></div>
                    <?php endforeach?>
                </div>
                <button class="btn btn-primary mt-4">提交</button>
            </form>
        </div>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
