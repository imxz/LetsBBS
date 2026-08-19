<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $isEdit = $node !== null; ?>
<div class="row admin-shell">
    <section class="col-lg-8 admin-main">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title"><?= $isEdit ? '编辑节点' : '添加节点' ?></h1>
            </div>
            <div class="panel-body">
                <form method="post" class="admin-form">
                    <?= csrf_field() ?>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="name">节点名称</label>
                        <div class="col-sm-9"><input class="form-control" id="name" name="name" maxlength="80"
                                value="<?= esc(old('name', $node['name'] ?? '')) ?>" required></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="slug">Slug</label>
                        <div class="col-sm-9"><input class="form-control" id="slug" name="slug"
                                pattern="[a-z0-9-]{2,80}" value="<?= esc(old('slug', $node['slug'] ?? '')) ?>" required>
                            <div class="form-text">仅使用小写字母、数字和连字符，用于稳定链接。</div>
                        </div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="parent_id">父节点</label>
                        <div class="col-sm-9"><select class="form-select" id="parent_id" name="parent_id">
                                <option value="0">无</option>
                                <?php foreach ($parents as $parent):?>
                                    <option value="<?= (int) $parent['id'] ?>"
                                        <?= (int) old('parent_id', $node['parent_id'] ?? 0) === (int) $parent['id'] ? 'selected' : '' ?>>
                                        <?= esc($parent['name']) ?></option>
                                <?php endforeach?>
                            </select></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="sort_order">排序</label>
                        <div class="col-sm-9"><input class="form-control" type="number" id="sort_order"
                                name="sort_order" value="<?= (int) old('sort_order', $node['sort_order'] ?? 0) ?>">
                        </div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="keywords">关键字</label>
                        <div class="col-sm-9"><input class="form-control" id="keywords" name="keywords" maxlength="160"
                                value="<?= esc(old('keywords', $node['keywords'] ?? '')) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="description">简介</label>
                        <div class="col-sm-9"><textarea class="form-control" id="description" name="description"
                                maxlength="255"
                                rows="3"><?= esc(old('description', $node['description'] ?? '')) ?></textarea></div>
                    </div>
                    <?php foreach ([['featured', '首页节点导航', 1], ['show_on_home', '首页置顶按钮', 0], ['is_active', '节点状态', 1]] as [$key, $label, $default]):?>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="<?= $key ?>"><?= $label ?></label>
                            <div class="col-sm-9"><select class="form-select" id="<?= $key ?>" name="<?= $key ?>">
                                    <option value="1"
                                        <?= (int) old($key, $node[$key] ?? $default) === 1 ? 'selected' : '' ?>>
                                        <?= $key === 'is_active' ? '启用' : '显示' ?></option>
                                    <option value="0"
                                        <?= (int) old($key, $node[$key] ?? $default) === 0 ? 'selected' : '' ?>>
                                        <?= $key === 'is_active' ? '停用' : '不显示' ?></option>
                                </select></div>
                        </div>
                    <?php endforeach?>
                    <div class="row">
                        <div class="offset-sm-3 col-sm-9"><button class="btn btn-primary">提交</button></div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
