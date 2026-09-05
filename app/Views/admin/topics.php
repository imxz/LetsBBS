<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $statusLabels = ['published' => '已发布', 'pending' => '待审核', 'hidden' => '已隐藏', 'deleted' => '已删除']; ?>
<div class="row admin-shell">
    <section class="col-lg-8 admin-main">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title"><?= esc($title) ?></h1>
            </div>
            <div class="panel-body table-responsive">
                <form class="d-flex gap-2 admin-search" method="get">
                    <?php if ($section !== 'topic-verify'): ?>
                        <select class="form-select" name="status" aria-label="主题状态">
                            <option value="">全部未删除</option>
                            <?php foreach ($statusLabels as $state => $label): ?>
                                <option value="<?= $state ?>" <?= $statusFilter === $state ? 'selected' : '' ?>>
                                    <?= $label ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php endif ?>
                    <input class="form-control" name="q" maxlength="80" placeholder="搜索标题"
                        value="<?= esc($query) ?>"><button class="btn btn-outline-secondary text-nowrap">提交</button>
                </form>
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>标题</th>
                            <th>作者</th>
                            <th>发布时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topics as $topic):?>
                            <tr>
                                <td><?= (int) $topic['id'] ?></td>
                                <td class="admin-title-cell"><a href="/topic/<?= (int) $topic['id'] ?>"
                                        target="_blank"><?= esc($topic['title']) ?></a>
                                    <div class="meta mt-1"><?= esc($topic['node_name']) ?> · <span
                                            class="status-badge status-<?= esc($topic['status']) ?>"><?= esc($statusLabels[$topic['status']] ?? $topic['status']) ?></span>
                                    </div>
                                </td>
                                <td><a href="/member/<?= esc($topic['username'], 'url') ?>"
                                        target="_blank"><?= esc($topic['username']) ?></a></td>
                                <td class="meta text-nowrap" title="<?= esc($topic['created_at']) ?>">
                                    <?= esc(forum_date($topic['created_at'])) ?></td>
                                <td>
                                    <div class="admin-actions"><a
                                            href="/admin/topic/<?= (int) $topic['id'] ?>/edit">编辑</a>
                                        <?= view('admin/_moderate', ['row' => $topic, 'resource' => 'topic']) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach?>

                        <?php if (!$topics):?>
                            <tr>
                                <td colspan="5" class="text-secondary text-center py-4">暂无数据。</td>
                            </tr>
                        <?php endif?>
                    </tbody>
                </table>
            </div>
        </div>
        <?= forum_pager($basePath, $page, $hasNext, ['q' => $query, 'status' => $statusFilter]) ?>

        <?php if ($section === 'topic-verify'):?>
            <div class="panel mt-4">
                <div class="panel-heading">
                    <h2 class="panel-title"><a href="/admin/comment?status=pending">进入回复审核与管理</a></h2>
                </div>
                <p class="p-3 text-secondary mb-0">回复已独立分页管理，可按状态和关键词筛选。</p>
            </div>
        <?php endif?>
    </section>
    <?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
