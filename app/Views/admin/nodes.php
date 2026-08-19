<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row admin-shell">
    <section class="col-lg-8 admin-main">
        <div class="panel">
            <div class="panel-heading d-flex justify-content-between align-items-center">
                <h1 class="panel-title">节点列表</h1><a class="btn btn-sm btn-primary" href="/admin/node/add">添加节点</a>
            </div>
            <div class="panel-body table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>节点</th>
                            <th>父节点</th>
                            <th>主题</th>
                            <th>显示</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nodes as $node):?>
                            <tr>
                                <td><?= (int) $node['id'] ?></td>
                                <td><a href="/node/<?= (int) $node['id'] ?>"
                                        target="_blank"><?= !empty($node['parent_id']) ? '　' : '' ?><?= esc($node['name']) ?></a>
                                    <div class="meta"><?= esc($node['slug']) ?></div>
                                </td>
                                <td><?= esc($node['parent_name'] ?? '—') ?></td>
                                <td><?= (int) $node['topic_count'] ?></td>
                                <td><span
                                        class="badge <?= $node['is_active'] ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $node['is_active'] ? '启用' : '停用' ?></span><?= $node['featured'] ? ' <span class="badge text-bg-light">首页</span>' : '' ?><?= $node['show_on_home'] ? ' <span class="badge text-bg-primary">置顶</span>' : '' ?>
                                </td>
                                <td><a href="/admin/node/<?= (int) $node['id'] ?>/edit">编辑</a></td>
                            </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
            </div>
        </div>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
