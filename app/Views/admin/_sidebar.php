<aside class="col-lg-4 sidebar admin-sidebar" aria-label="后台管理菜单">
    <nav class="list-group admin-nav-group">
        <a href="/admin/topic" class="list-group-item list-group-item-action admin-group-title">主题</a>
        <a href="/admin/topic"
            class="list-group-item list-group-item-action <?= $section === 'topic' ? 'current' : '' ?>">所有主题</a>
        <a href="/admin/topic/verify"
            class="list-group-item list-group-item-action <?= $section === 'topic-verify' ? 'current' : '' ?>">待审主题</a>
    </nav>
    <nav class="list-group admin-nav-group">
        <a href="/admin/user" class="list-group-item list-group-item-action admin-group-title">用户</a>
        <a href="/admin/user"
            class="list-group-item list-group-item-action <?= $section === 'user' ? 'current' : '' ?>">所有用户</a>
        <a href="/admin/user/banned"
            class="list-group-item list-group-item-action <?= $section === 'user-banned' ? 'current' : '' ?>">禁言用户</a>
    </nav>
    <nav class="list-group admin-nav-group">
        <a href="/admin/node" class="list-group-item list-group-item-action admin-group-title">节点</a>
        <a href="/admin/node"
            class="list-group-item list-group-item-action <?= $section === 'node' ? 'current' : '' ?>">节点列表</a>
        <a href="/admin/node/add"
            class="list-group-item list-group-item-action <?= $section === 'node-add' ? 'current' : '' ?>">添加节点</a>
    </nav>
    <nav class="list-group admin-nav-group">
        <a href="/admin/settings/site" class="list-group-item list-group-item-action admin-group-title">网站</a>
        <a href="/admin/settings/site"
            class="list-group-item list-group-item-action <?= $section === 'settings-site' ? 'current' : '' ?>">基本设置</a>
        <a href="/admin/settings/verify"
            class="list-group-item list-group-item-action <?= $section === 'settings-verify' ? 'current' : '' ?>">审核设置</a>
    </nav>
</aside>
