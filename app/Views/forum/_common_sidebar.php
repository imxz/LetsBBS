<aside class="col-lg-4 sidebar">
    <section class="panel mb-3">
        <div class="p-3 border-bottom">
            <h2 class="h6 mb-0">你好<?= $viewer ? '，' . esc($viewer['username']) : '' ?></h2>
        </div>
        <div class="p-3">
            <?php if ($viewer):?>
                <div class="d-flex align-items-center gap-3 mb-3"><a class="avatar"
                        href="/member/<?= esc($viewer['username'], 'url') ?>">
                        <?php if ($viewer['avatar']):?>
                            <img class="avatar" src="<?= esc($viewer['avatar']) ?>"
                                alt="<?= esc($viewer['username']) ?> 的头像">
                        <?php else:?>
                            <?= esc(strtoupper(substr($viewer['username'], 0, 1))) ?>

                        <?php endif?>
                    </a><a href="/member/<?= esc($viewer['username'], 'url') ?>"><?= esc($viewer['username']) ?></a>
                </div>
                <div class="row g-0 text-center follow-summary"><a class="col"
                        href="/?filter=nodes"><strong><?= (int) $viewer['node_follows'] ?></strong><span>节点收藏</span></a><a
                        class="col"
                        href="/?filter=topics"><strong><?= (int) $viewer['topic_follows'] ?></strong><span>主题收藏</span></a><a
                        class="col"
                        href="/?filter=users"><strong><?= (int) $viewer['user_follows'] ?></strong><span>特别关注</span></a>
                </div>
            <?php else:?>
                <p class="mb-0"><?= nl2br(esc($siteIntroduction)) ?></p>
            <?php endif?>
        </div>
        <div class="px-3 py-2 border-top">
            <?php if ($viewer):?>
                <a href="/notification"><?= (int) $viewer['unread_notifications'] ?> 条未读提醒</a>
            <?php else:?>
                <a href="/reg">注册</a>　<a href="/login">登录</a>
            <?php endif?>
        </div>
    </section>
    <section class="panel p-3 mb-3 text-center text-secondary"><small>这里是预设的广告位，可在新版侧栏模板中替换为社区公告或广告内容。</small></section>
    <section class="panel p-3">
        <h2 class="h6">社区运行状况</h2>
        <p class="mb-1">注册会员：<?= (int) $statistics['users'] ?></p>
        <p class="mb-1">　　主题：<?= (int) $statistics['topics'] ?></p>
        <p class="mb-0">　　回复：<?= (int) $statistics['comments'] ?></p>
    </section>
</aside>
