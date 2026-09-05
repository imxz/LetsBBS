<details class="my-2 small">
    <summary>举报<?= $commentId ? '此回复' : '此主题' ?></summary>
    <form class="d-flex gap-2 my-2" method="post" action="/topic/<?= (int) $topicId ?>/report">
        <?= csrf_field() ?>
        <input type="hidden" name="comment_id" value="<?= (int) $commentId ?>"><input
            class="form-control form-control-sm" name="reason" maxlength="500" placeholder="请说明举报理由" required><button
            class="btn btn-sm btn-outline-secondary">提交举报</button>
    </form>
</details>
