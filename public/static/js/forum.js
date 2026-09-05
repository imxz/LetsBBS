/* Site interactions and a shared editor whose formats match HtmlSanitizer. */
(() => {
    const config = window.forumConfig || {};
    const draftPrefix = `letsbbs-draft-${config.userId}-`;
    const storage = {
        get(key) {
            try {
                return JSON.parse(sessionStorage.getItem(key));
            } catch {
                return null;
            }
        },
        put(key, value) {
            try {
                sessionStorage.setItem(key, JSON.stringify(value));
            } catch {}
        },
        remove(key) {
            try {
                sessionStorage.removeItem(key);
            } catch {}
        },
    };
    if (config.clearDraft) storage.remove(draftPrefix + config.clearDraft);
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const button = event.submitter;
            if (button?.dataset.confirm && !window.confirm(button.dataset.confirm)) event.preventDefault();
            if (form.action.endsWith('/logout')) {
                try {
                    Object.keys(sessionStorage)
                        .filter((key) => key.startsWith(draftPrefix))
                        .forEach((key) => storage.remove(key));
                } catch {}
            }
        });
    });
    const avatar = document.querySelector('#avatar');
    let previewUrl;
    avatar?.addEventListener('change', () => {
        const preview = document.querySelector('#avatar-preview');
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        if (avatar.files[0] && preview) {
            previewUrl = URL.createObjectURL(avatar.files[0]);
            preview.src = previewUrl;
            preview.classList.remove('d-none');
        }
    });
    document.querySelectorAll('.post-body').forEach((body) => {
        const walker = document.createTreeWalker(body, NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (walker.nextNode())
            if (!walker.currentNode.parentElement.closest('a,pre,code,textarea')) nodes.push(walker.currentNode);
        for (const node of nodes) {
            const regex = /(?<![a-z0-9_.-])@([a-z0-9]{3,12})(?![a-z0-9])/gi;
            const matches = [...node.textContent.matchAll(regex)];
            if (!matches.length) continue;
            const fragment = document.createDocumentFragment();
            let offset = 0;
            for (const match of matches) {
                fragment.append(document.createTextNode(node.textContent.slice(offset, match.index)));
                const link = document.createElement('a');
                link.href = '/member/' + encodeURIComponent(match[1].toLowerCase());
                link.textContent = match[0];
                fragment.append(link);
                offset = match.index + match[0].length;
            }
            fragment.append(document.createTextNode(node.textContent.slice(offset)));
            node.replaceWith(fragment);
        }
    });
    if (!window.tinymce) return;
    tinymce.addI18n('zh_CN', {
        Undo: '撤销',
        Redo: '重做',
        Bold: '加粗',
        Italic: '斜体',
        Underline: '下划线',
        Strikethrough: '删除线',
        Blocks: '段落格式',
        Paragraph: '正文',
        Headings: '标题',
        'Heading 2': '标题 2',
        'Heading 3': '标题 3',
        'Heading 4': '标题 4',
        Preformatted: '代码块',
        'Font sizes': '字号',
        'Font size': '字号',
        'Text color': '文字颜色',
        'Text color {0}': '文字颜色：{0}',
        'Text color menu': '选择文字颜色',
        'Clear formatting': '清除格式',
        Black: '黑色',
        'Custom color': '自定义颜色',
        'Remove color': '清除颜色',
        'Align left': '左对齐',
        'Align center': '居中',
        'Align right': '右对齐',
        'Bullet list': '无序列表',
        'Numbered list': '有序列表',
        'Decrease indent': '减少缩进',
        'Increase indent': '增加缩进',
        Blockquote: '引用',
        'Horizontal line': '分隔线',
        'Remove formatting': '清除格式',
        Preview: '预览',
        Close: '关闭',
        Cancel: '取消',
        Save: '保存',
        Ok: '确定',
        'Insert/edit link': '插入或编辑链接',
        'Insert link': '插入链接',
        'Remove link': '取消链接',
        'Open link': '打开链接',
        'Link...': '链接…',
        'Text to display': '显示文字',
        Title: '标题',
        URL: '地址',
        'Open link in...': '打开方式',
        'Current window': '当前窗口',
        'New window': '新窗口',
        None: '无',
        'Insert/edit image': '插入或编辑图片',
        'Image...': '图片…',
        General: '常规',
        Advanced: '高级',
        Source: '来源',
        'Alternative description': '替代文字',
        Width: '宽度',
        Height: '高度',
        Dimensions: '尺寸',
        'Constrain proportions': '保持比例',
        Upload: '上传',
        'Browse for an image': '选择图片',
        'Drop an image here': '拖入图片',
        OR: '或',
        'Uploading image': '正在上传图片',
        Image: '图片',
        'Rich Text Area': '富文本编辑区',
        'More...': '更多…',
        More: '更多',
        Edit: '编辑',
        Insert: '插入',
        View: '视图',
        Format: '格式',
        Tools: '工具',
        'Select all': '全选',
        Copy: '复制',
        Cut: '剪切',
        Paste: '粘贴',
        'Select...': '选择…',
        'No color': '无颜色',
        'Color Picker': '选择颜色',
        Color: '颜色',
        'Red component': '红色分量',
        'Green component': '绿色分量',
        'Blue component': '蓝色分量',
        'Hex color code': '十六进制色值',
        Anchor: '锚点',
        'Block {0}': '段落：{0}',
        'Font size {0}': '字号：{0}',
        'Element Path': '当前格式',
        'Press the Up and Down arrow keys to resize the editor.': '按上下方向键调整编辑器大小。',
        'Reveal or hide additional toolbar items': '显示或收起更多工具',
    });
    const forms = new Map();
    document.querySelectorAll('form[data-draft-key]').forEach((form) => {
        const key = draftPrefix + form.dataset.draftKey;
        let saved = storage.get(key);
        if (saved && Date.now() - saved.savedAt >= 86400000) {
            storage.remove(key);
            saved = null;
        }
        let restored = false;
        if (saved) {
            for (const name of ['body', 'title', 'node_id'])
                if (form.elements[name] && !form.elements[name].value) {
                    form.elements[name].value = saved[name] || '';
                    restored = true;
                }
        }
        const status = document.createElement('div');
        status.className = 'form-text my-2';
        status.textContent = restored ? '已恢复本标签页草稿。' : '草稿自动保存在当前标签页，退出登录时清除。';
        if (saved && !restored) {
            const restore = document.createElement('button');
            restore.type = 'button';
            restore.className = 'btn btn-sm btn-link';
            restore.textContent = '恢复未提交草稿';
            restore.addEventListener('click', () => {
                for (const name of ['body', 'title', 'node_id'])
                    if (form.elements[name]) form.elements[name].value = saved[name] || '';
                tinymce.get(form.elements.body?.id)?.setContent(saved.body || '');
                status.textContent = '已恢复本标签页草稿，请确认内容后保存。';
                restore.remove();
            });
            form.append(restore);
        }
        const discard = document.createElement('button');
        discard.type = 'button';
        discard.className = 'btn btn-sm btn-link';
        discard.textContent = '清空草稿';
        discard.addEventListener('click', () => {
            if (!confirm('清空当前草稿？')) return;
            storage.remove(key);
            const editor = tinymce.get(form.elements.body?.id);
            if (editor) editor.setContent('');
            else if (form.elements.body) form.elements.body.value = '';
            for (const name of ['title', 'node_id']) if (form.elements[name]) form.elements[name].value = '';
            storage.remove(key);
            status.textContent = '草稿已清空。';
        });
        form.append(status, discard);
        const save = () => {
            const data = { savedAt: Date.now() };
            for (const name of ['body', 'title', 'node_id']) data[name] = form.elements[name]?.value || '';
            storage.put(key, data);
            status.textContent = '草稿已保存在当前标签页。';
        };
        form.addEventListener('input', save);
        form.addEventListener('submit', async (event) => {
            if (form.dataset.uploadReady === '1') return;
            event.preventDefault();
            const editor = tinymce.get(form.elements.body?.id);
            const button = event.submitter;
            if (form.dataset.busy === '1') return;
            form.dataset.busy = '1';
            if (button) button.disabled = true;
            try {
                if (editor) {
                    const uploads = await editor.uploadImages();
                    if (uploads.some((result) => result.status === false)) throw new Error('图片上传未完成，请重试。');
                    editor.save();
                }
                save();
                form.dataset.uploadReady = '1';
                if (button) button.disabled = false;
                form.requestSubmit(button || undefined);
            } catch (error) {
                status.textContent = error.message;
                form.dataset.busy = '0';
                if (button) button.disabled = false;
            }
        });
        forms.set(form, save);
    });
    tinymce.init({
        toolbar_mode: 'wrap',
        selector: 'textarea.js-editor',
        license_key: 'gpl',
        language: 'zh_CN',
        menubar: false,
        branding: false,
        promotion: false,
        height: 360,
        plugins: 'image link lists preview',
        toolbar:
            'undo redo | blocks fontsize | bold italic underline strikethrough forecolor | blockquote bullist numlist hr | alignleft aligncenter alignright | link unlink image | forumemoji forumsource forumfullscreen | removeformat preview',
        block_formats: '正文=p;标题 2=h2;标题 3=h3;标题 4=h4;代码块=pre',
        font_size_formats: '12px 14px 16px 18px 24px 32px',
        automatic_uploads: true,
        paste_data_images: false,
        images_file_types: 'jpeg,jpg,png,gif,webp',
        invalid_elements: 'script,iframe,object,embed,video,audio,source,svg',
        relative_urls: false,
        remove_script_host: true,
        images_upload_handler: (blobInfo) =>
            new Promise((resolve, reject) => {
                const data = new FormData();
                data.append('file', blobInfo.blob(), blobInfo.filename());
                data.append(config.csrfName, config.csrfHash);
                fetch('/media/images', { method: 'POST', credentials: 'same-origin', body: data })
                    .then(async (response) => {
                        const json = await response.json().catch(() => ({}));
                        if (!response.ok || !json.location) throw new Error(json.error || '图片上传失败，请重试。');
                        resolve(json.location);
                    })
                    .catch((error) => reject(error.message));
            }),
        setup(editor) {
            editor.ui.registry.addButton('forumfullscreen', {
                text: '全屏',
                tooltip: '切换全屏编辑',
                onAction: () => {
                    editor.getContainer().classList.toggle('forum-editor-fullscreen');
                },
            });
            editor.ui.registry.addMenuButton('forumemoji', {
                text: '表情',
                fetch(callback) {
                    callback(
                        ['😀', '😊', '👍', '🎉', '❤️', '🤔', '🙏', '🚀'].map((text) => ({
                            type: 'menuitem',
                            text,
                            onAction: () => editor.insertContent(text),
                        })),
                    );
                },
            });
            editor.ui.registry.addButton('forumsource', {
                text: '源码',
                onAction: () =>
                    editor.windowManager.open({
                        title: '编辑 HTML 源码',
                        size: 'large',
                        body: {
                            type: 'panel',
                            items: [{ type: 'textarea', name: 'source', label: '仅支持安全的正文格式' }],
                        },
                        initialData: { source: editor.getContent() },
                        buttons: [
                            { type: 'cancel', text: '取消' },
                            { type: 'submit', text: '保存', primary: true },
                        ],
                        onSubmit(dialog) {
                            editor.setContent(dialog.getData().source);
                            editor.save();
                            forms.get(editor.getElement().form)?.();
                            dialog.close();
                        },
                    }),
            });
            editor.on('change input undo redo', () => {
                editor.save();
                forms.get(editor.getElement().form)?.();
            });
            editor.on('BeforeSetContent', (event) => {
                if (/<img[^>]+src=["'](?:data:|https?:|\/\/)/i.test(event.content)) {
                    event.preventDefault();
                    editor.windowManager.alert('请上传本地图片，不能粘贴 Base64 或远程图片。');
                }
            });
        },
    });
})();
