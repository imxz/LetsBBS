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
                        <div class="row">
                            <div class="offset-sm-3 col-sm-9"><button class="btn btn-primary">提交</button></div>
                        </div>
                    </form>
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
