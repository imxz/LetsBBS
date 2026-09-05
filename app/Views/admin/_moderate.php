<form class="d-flex flex-wrap gap-2 mt-2" method="post"
    action="/admin/<?= esc($resource) ?>/<?= (int) $row['id'] ?>/moderate">
    <?= csrf_field() ?>
    <input class="form-control form-control-sm" name="reason" maxlength="500" placeholder="审核理由（可选）" aria-label="审核理由">
    <?php if ($row['status'] === 'deleted'): ?>
        <button class="btn btn-sm btn-outline-primary" name="status" value="pending">恢复到待审核</button>
    <?php else: ?>

        <?php if ($row['status'] !== 'published'): ?>
            <button class="btn btn-sm btn-success" name="status" value="published">通过</button>
        <?php endif ?>

        <?php if ($row['status'] === 'published'): ?>
            <button class="btn btn-sm btn-outline-warning" name="status" value="hidden">隐藏</button>
        <?php endif ?>
        <button class="btn btn-sm btn-outline-danger" name="status" value="deleted"
            data-confirm="确认将内容移入回收站？">删除</button>
    <?php endif ?>
</form>
