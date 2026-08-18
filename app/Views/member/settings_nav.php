<nav class="nav nav-pills gap-2 mb-4" aria-label="个人设置">
    <a class="nav-link <?= $active === 'profile' ? 'active' : '' ?>" href="/settings/profile">个人资料</a>
    <a class="nav-link <?= $active === 'avatar' ? 'active' : '' ?>" href="/settings/avatar">上传头像</a>
    <a class="nav-link <?= $active === 'password' ? 'active' : '' ?>" href="/settings/password">更改密码</a>
</nav>
