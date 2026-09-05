<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row admin-shell">
    <section class="col-lg-8 admin-main">
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title"><?= $mode === 'site' ? '基本设置' : '审核设置' ?></h1>
            </div>
            <div class="panel-body">
                <?php if ($mode === 'site'):?>
                    <form method="post" class="admin-form">
                        <?= csrf_field() ?>

                        <?php foreach ([['site_name', '网站名', 80], ['site_subtitle', '网站副标题', 120], ['home_welcome_message', '欢迎信息', 120], ['site_keywords', '关键词', 255]] as [$key, $label, $max]):?>
                            <div class="row form-row"><label class="col-sm-3 col-form-label"
                                    for="<?= $key ?>"><?= $label ?></label>
                                <div class="col-sm-9"><input class="form-control" id="<?= $key ?>" name="<?= $key ?>"
                                        maxlength="<?= $max ?>"
                                        value="<?= esc(old($key, $settings[$key] ?? ($key === 'site_name' ? 'LetsBBS' : ''))) ?>"
                                        <?= $key === 'site_name' ? 'required' : '' ?>></div>
                            </div>
                        <?php endforeach?>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="site_description">网站描述</label>
                            <div class="col-sm-9"><textarea class="form-control" id="site_description"
                                    name="site_description" maxlength="500"
                                    rows="3"><?= esc(old('site_description', $settings['site_description'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="home_introduction">网站简介</label>
                            <div class="col-sm-9"><textarea class="form-control" id="home_introduction"
                                    name="home_introduction" maxlength="1000"
                                    rows="4"><?= esc(old('home_introduction', $settings['home_introduction'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="registration_enabled">开放注册</label>
                            <div class="col-sm-9"><select class="form-select" name="registration_enabled"
                                    id="registration_enabled">
                                    <option value="1"
                                        <?= ($settings['registration_enabled'] ?? '1') === '1' ? 'selected' : '' ?>>开放
                                    </option>
                                    <option value="0"
                                        <?= ($settings['registration_enabled'] ?? '1') === '0' ? 'selected' : '' ?>>关闭
                                    </option>
                                </select>
                                <div class="form-text">邮箱必填；无需 SMTP。关闭后新用户不能注册，已有账号仍可登录。</div>
                            </div>
                        </div>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="home_node_limit">首页节点数</label>
                            <div class="col-sm-9"><input class="form-control" type="number" min="0" max="100"
                                    name="home_node_limit" id="home_node_limit"
                                    value="<?= (int) ($settings['home_node_limit'] ?? 10) ?>">
                                <div class="form-text">只显示勾选“首页显示”的节点，0 表示不展示节点快捷入口。</div>
                            </div>
                        </div>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="site_timezone">显示时区</label>
                            <div class="col-sm-9"><select class="form-select" name="site_timezone" id="site_timezone">
                                    <?php foreach (\DateTimeZone::listIdentifiers() as $zone): ?>
                                        <option value="<?= esc($zone) ?>"
                                            <?= ($settings['site_timezone'] ?? 'Asia/Shanghai') === $zone ? 'selected' : '' ?>>
                                            <?= esc($zone) ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="form-text">仅改变显示方式，数据库仍统一使用 UTC。</div>
                            </div>
                        </div>
                        <div class="row form-row"><label class="col-sm-3 col-form-label" for="footer_text">页脚文字</label>
                            <div class="col-sm-9"><textarea class="form-control" name="footer_text" id="footer_text"
                                    maxlength="500" rows="2"><?= esc($settings['footer_text'] ?? '') ?></textarea>
                                <div class="form-text">仅允许纯文字，不执行 HTML 或第三方脚本。</div>
                            </div>
                        </div>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="analytics_enabled">本地访问统计</label>
                            <div class="col-sm-9"><select class="form-select" name="analytics_enabled"
                                    id="analytics_enabled">
                                    <option value="0"
                                        <?= ($settings['analytics_enabled'] ?? '0') === '0' ? 'selected' : '' ?>>关闭（默认）
                                    </option>
                                    <option value="1"
                                        <?= ($settings['analytics_enabled'] ?? '0') === '1' ? 'selected' : '' ?>>启用
                                    </option>
                                </select>
                                <div class="form-text">仅按站点时区累计公开页面访问量，重复访问也计数；不记录 IP、账号或访客标识，不发送至第三方。关闭后保留已有统计。</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-sm-3 col-sm-9"><button class="btn btn-primary">提交</button></div>
                        </div>
                    </form>
                    <?php if (!empty($pageViews)): ?>
                        <details class="mt-3">
                            <summary>最近 30 个有访问记录的日期</summary>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>日期</th>
                                        <th>公开页面访问量</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pageViews as $day): ?>
                                        <tr>
                                            <td><?= esc($day['day']) ?></td>
                                            <td><?= (int) $day['views'] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </details>
                    <?php endif ?>

                <?php else:?>
                    <form method="post" class="admin-form">
                        <?= csrf_field() ?>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="topic_requires_approval">主题审核</label>
                            <div class="col-sm-9"><select class="form-select" id="topic_requires_approval"
                                    name="topic_requires_approval">
                                    <option value="1"
                                        <?= ($settings['topic_requires_approval'] ?? '0') === '1' ? 'selected' : '' ?>>
                                        新主题需要审核</option>
                                    <option value="0"
                                        <?= ($settings['topic_requires_approval'] ?? '0') === '0' ? 'selected' : '' ?>>
                                        新主题不需要审核</option>
                                </select>
                                <div class="form-text">启用后，普通用户的新主题会进入“待审主题”，管理员通过后才公开显示。</div>
                            </div>
                        </div>
                        <div class="row form-row"><label class="col-sm-3 col-form-label"
                                for="comment_requires_approval">回复审核</label>
                            <div class="col-sm-9"><select class="form-select" id="comment_requires_approval"
                                    name="comment_requires_approval">
                                    <option value="1"
                                        <?= ($settings['comment_requires_approval'] ?? '0') === '1' ? 'selected' : '' ?>>
                                        新回复需要审核</option>
                                    <option value="0"
                                        <?= ($settings['comment_requires_approval'] ?? '0') === '0' ? 'selected' : '' ?>>
                                        新回复不需要审核</option>
                                </select>
                                <div class="form-text">启用后，普通用户的新回复在管理员通过前不会计数、通知或公开显示。</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-sm-3 col-sm-9"><button class="btn btn-primary">提交</button></div>
                        </div>
                    </form>
                <?php endif?>
            </div>
        </div>
    </section>
    <?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
