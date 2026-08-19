<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row admin-shell">
    <section class="col-lg-8 admin-main">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title">用户编辑</h1>
            </div>
            <div class="panel-body">
                <form method="post" class="admin-form">
                    <?= csrf_field() ?>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="username">用户名</label>
                        <div class="col-sm-9"><input class="form-control" id="username" name="username" maxlength="30"
                                value="<?= esc(old('username', $member['username'])) ?>" required>
                            <div class="form-text">修改前请确认用户名未被使用。</div>
                        </div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="email">电子邮件</label>
                        <div class="col-sm-9"><input class="form-control" type="email" id="email" name="email"
                                value="<?= esc(old('email', $member['email'])) ?>" required></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="qq">QQ</label>
                        <div class="col-sm-9"><input class="form-control" id="qq" name="qq" maxlength="20"
                                value="<?= esc(old('qq', $member['qq'])) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="location">所在地</label>
                        <div class="col-sm-9"><input class="form-control" id="location" name="location" maxlength="120"
                                value="<?= esc(old('location', $member['location'])) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="homepage">个人主页</label>
                        <div class="col-sm-9"><input class="form-control" type="url" id="homepage" name="homepage"
                                maxlength="255" value="<?= esc(old('homepage', $member['homepage'])) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="signature">签名</label>
                        <div class="col-sm-9"><input class="form-control" id="signature" name="signature"
                                maxlength="160" value="<?= esc(old('signature', $member['signature'])) ?>"></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="bio">个人简介</label>
                        <div class="col-sm-9"><textarea class="form-control" id="bio" name="bio" maxlength="500"
                                rows="4"><?= esc(old('bio', $member['bio'])) ?></textarea></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="active">账号状态</label>
                        <div class="col-sm-9"><select class="form-select" id="active" name="active">
                                <option value="1" <?= (int) old('active', $member['active']) === 1 ? 'selected' : '' ?>>
                                    正常</option>
                                <option value="0" <?= (int) old('active', $member['active']) === 0 ? 'selected' : '' ?>>
                                    停用登录</option>
                            </select></div>
                    </div>
                    <div class="row form-row"><label class="col-sm-3 col-form-label" for="reset_avatar">恢复头像</label>
                        <div class="col-sm-9"><select class="form-select" id="reset_avatar" name="reset_avatar">
                                <option value="0">保留现有头像</option>
                                <option value="1">恢复默认头像</option>
                            </select></div>
                    </div>
                    <div class="row">
                        <div class="offset-sm-3 col-sm-9"><button class="btn btn-primary">提交</button></div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
