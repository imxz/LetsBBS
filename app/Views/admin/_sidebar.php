<aside class="col-lg-4 sidebar">
    <div class="panel overflow-hidden">
        <div class="p-3 border-bottom"><strong>后台管理</strong></div>
        <nav class="list-group list-group-flush" aria-label="后台管理菜单">
            <div class="px-3 pt-3 pb-1 meta">主题</div>
            <a class="list-group-item list-group-item-action <?= $section === 'topic' ? 'active' : '' ?>"
                href="/admin/topic">所有主题</a>
            <a class="list-group-item list-group-item-action <?= $section === 'topic-verify' ? 'active' : '' ?>"
                href="/admin/topic/verify">待审主题</a>
            <div class="px-3 pt-3 pb-1 meta">用户</div>
            <a class="list-group-item list-group-item-action <?= $section === 'user' ? 'active' : '' ?>"
                href="/admin/user">所有用户</a>
            <a class="list-group-item list-group-item-action <?= $section === 'user-banned' ? 'active' : '' ?>"
                href="/admin/user/banned">禁言用户</a>
            <div class="px-3 pt-3 pb-1 meta">节点</div>
            <a class="list-group-item list-group-item-action <?= $section === 'node' ? 'active' : '' ?>"
                href="/admin/node">节点列表</a>
            <a class="list-group-item list-group-item-action <?= $section === 'node-add' ? 'active' : '' ?>"
                href="/admin/node/add">添加节点</a>
            <div class="px-3 pt-3 pb-1 meta">网站</div>
            <a class="list-group-item list-group-item-action <?= $section === 'settings-site' ? 'active' : '' ?>"
                href="/admin/settings/site">基本设置</a>
            <a class="list-group-item list-group-item-action <?= $section === 'settings-verify' ? 'active' : '' ?>"
                href="/admin/settings/verify">审核设置</a>
        </nav>
    </div>
</aside>
