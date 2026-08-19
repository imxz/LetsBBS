<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <section class="col-lg-8">
        <div class="panel p-4">
            <h1 class="h5 border-bottom pb-3 mb-3"><?= $mode === 'site' ? '基本设置' : '审核设置' ?></h1>
            <?php if ($mode === 'site'):?>
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label" for="site_name">网站名</label><input class="form-control"
                            id="site_name" name="site_name" maxlength="80"
                            value="<?= esc(old('site_name', $settings['site_name'] ?? 'LetsBBS')) ?>" required></div>
                    <div class="mb-3"><label class="form-label" for="site_subtitle">网站副标题</label><input
                            class="form-control" id="site_subtitle" name="site_subtitle" maxlength="120"
                            value="<?= esc(old('site_subtitle', $settings['site_subtitle'] ?? '')) ?>"></div>
                    <div class="mb-3"><label class="form-label" for="home_welcome_message">欢迎信息</label><input
                            class="form-control" id="home_welcome_message" name="home_welcome_message" maxlength="120"
                            value="<?= esc(old('home_welcome_message', $settings['home_welcome_message'] ?? '')) ?>">
                    </div>
                    <div class="mb-3"><label class="form-label" for="site_keywords">关键词</label><input
                            class="form-control" id="site_keywords" name="site_keywords" maxlength="255"
                            value="<?= esc(old('site_keywords', $settings['site_keywords'] ?? '')) ?>"></div>
                    <div class="mb-3"><label class="form-label" for="site_description">网站描述</label><textarea
                            class="form-control" id="site_description" name="site_description" maxlength="500"
                            rows="3"><?= esc(old('site_description', $settings['site_description'] ?? '')) ?></textarea>
                    </div>
                    <div class="mb-3"><label class="form-label" for="home_introduction">网站简介</label><textarea
                            class="form-control" id="home_introduction" name="home_introduction" maxlength="1000"
                            rows="4"><?= esc(old('home_introduction', $settings['home_introduction'] ?? '')) ?></textarea>
                    </div>
                    <button class="btn btn-primary">提交</button>
                </form>
            <?php else:?>
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label" for="topic_requires_approval">主题审核</label><select
                            class="form-select" id="topic_requires_approval" name="topic_requires_approval">
                            <option value="1"
                                <?= ($settings['topic_requires_approval'] ?? '0') === '1' ? 'selected' : '' ?>>新主题需要审核
                            </option>
                            <option value="0"
                                <?= ($settings['topic_requires_approval'] ?? '0') === '0' ? 'selected' : '' ?>>新主题不需要审核
                            </option>
                        </select>
                        <div class="form-text">启用后，普通用户的新主题会进入“待审主题”，管理员通过后才公开显示。</div>
                    </div><button class="btn btn-primary">提交</button>
                </form>
            <?php endif?>
        </div>
    </section><?= $this->include('admin/_sidebar') ?>
</div>
<?= $this->endSection() ?>
