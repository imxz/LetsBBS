<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel">
            <div class="p-3 border-bottom">
                <h1 class="h5 mb-0"><?= esc($title) ?></h1>
            </div>
            <div class="p-3 table-responsive">
                <form class="d-flex gap-2 mb-3" method="get"><input class="form-control" name="q" maxlength="80"
                        placeholder="搜索用户名" value="<?= esc($query) ?>"><button
                        class="btn btn-outline-secondary">提交</button></form>
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>用户名</th>
                            <th>邮箱</th>
                            <th>注册时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $member):?>
                            <tr>
                                <td><?= (int) $member['id'] ?></td>
                                <td><a href="/member/<?= esc($member['username'], 'url') ?>"
                                        target="_blank"><?= esc($member['username']) ?></a><?= !$member['active'] ? ' <span class="badge text-bg-secondary">停用</span>' : '' ?>
                                </td>
                                <td><?= esc($member['email'] ?? '') ?></td>
                                <td class="meta text-nowrap"><?= esc($member['created_at']) ?></td>
                                <td class="text-nowrap"><a href="/admin/user/<?= (int) $member['id'] ?>/edit">编辑</a>
                                    <form class="d-inline" method="post"
                                        action="/admin/user/<?= (int) $member['id'] ?>/mute">
                                        <?= csrf_field() ?>
                                        <button
                                            class="btn btn-sm btn-link p-0 <?= $member['is_muted'] ? 'text-success' : 'text-danger' ?>"><?= $member['is_muted'] ? '激活发言' : '禁言' ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach?>

                        <?php if (!$users):?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">暂无用户。</td>
                            </tr>
                        <?php endif?>
                    </tbody>
                </table>
            </div>
        </div>
        <nav class="mt-3 d-flex justify-content-between">
            <?= $page > 1 ? '<a class="btn btn-outline-secondary" href="' . $basePath . '/' . ($page - 1) . ($query !== '' ? '?q=' . urlencode($query) : '') . '">上一页</a>' : '<span></span>' ?>

            <?php if ($hasNext):?>
                <a class="btn btn-outline-secondary"
                    href="<?= $basePath ?>/<?= $page + 1 ?><?= $query !== '' ? '?q=' . urlencode($query) : '' ?>">下一页</a>
            <?php endif?>
        </nav>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
