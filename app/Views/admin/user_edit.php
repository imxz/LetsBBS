<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel p-4">
            <h1 class="h5 border-bottom pb-3 mb-3">用户编辑</h1>
            <form method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="username">用户名</label><input
                            class="form-control" id="username" name="username" maxlength="30"
                            value="<?= esc(old('username', $member['username'])) ?>" required>
                        <div class="form-text">修改前请确认用户名未被使用。</div>
                    </div>
                    <div class="col-md-6"><label class="form-label" for="email">电子邮件</label><input class="form-control"
                            type="email" id="email" name="email" value="<?= esc(old('email', $member['email'])) ?>"
                            required></div>
                    <div class="col-md-6"><label class="form-label" for="qq">QQ</label><input class="form-control"
                            id="qq" name="qq" maxlength="20" value="<?= esc(old('qq', $member['qq'])) ?>"></div>
                    <div class="col-md-6"><label class="form-label" for="location">所在地</label><input
                            class="form-control" id="location" name="location" maxlength="120"
                            value="<?= esc(old('location', $member['location'])) ?>"></div>
                    <div class="col-12"><label class="form-label" for="homepage">个人主页</label><input class="form-control"
                            type="url" id="homepage" name="homepage" maxlength="255"
                            value="<?= esc(old('homepage', $member['homepage'])) ?>"></div>
                    <div class="col-12"><label class="form-label" for="signature">签名</label><input class="form-control"
                            id="signature" name="signature" maxlength="160"
                            value="<?= esc(old('signature', $member['signature'])) ?>"></div>
                    <div class="col-12"><label class="form-label" for="bio">个人简介</label><textarea class="form-control"
                            id="bio" name="bio" maxlength="500"
                            rows="4"><?= esc(old('bio', $member['bio'])) ?></textarea></div>
                    <div class="col-md-6"><label class="form-label" for="active">账号状态</label><select class="form-select"
                            id="active" name="active">
                            <option value="1" <?= (int) old('active', $member['active']) === 1 ? 'selected' : '' ?>>正常
                            </option>
                            <option value="0" <?= (int) old('active', $member['active']) === 0 ? 'selected' : '' ?>>停用登录
                            </option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label" for="reset_avatar">恢复头像</label><select
                            class="form-select" id="reset_avatar" name="reset_avatar">
                            <option value="0">保留现有头像</option>
                            <option value="1">恢复默认头像</option>
                        </select></div>
                </div>
                <button class="btn btn-primary mt-4">提交</button>
            </form>
        </div>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
