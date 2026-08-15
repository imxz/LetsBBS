import { promises as fs } from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';

import beautify from 'js-beautify';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const viewsRoot = path.join(root, 'app', 'Views');
const mode = process.argv.includes('--check') ? 'check' : process.argv.includes('--write') ? 'write' : null;

if (mode === null) {
    console.error('Usage: node tools/format-views.mjs --write|--check');
    process.exit(2);
}

const options = {
    indent_char: ' ',
    indent_handlebars: false,
    indent_inner_html: false,
    indent_scripts: 'keep',
    indent_size: 4,
    indent_with_tabs: false,
    inline_custom_elements: false,
    content_unformatted: ['pre', 'code', 'textarea', 'script', 'style'],
    extra_liners: ['head', 'body', '/html'],
    max_preserve_newlines: 1,
    preserve_newlines: true,
    wrap_attributes: 'auto',
    wrap_line_length: 120,
    html: {
        end_with_newline: true,
        js: {
            end_with_newline: false,
            templating: 'php',
        },
    },
};

const requestedFiles = process.argv.slice(2).filter((argument) => !argument.startsWith('--'));
const files = requestedFiles.length === 0 ? await listPhpFiles(viewsRoot) : requestedFiles.map(resolveViewFile);
const changed = [];

for (const file of files) {
    const input = (await fs.readFile(file, 'utf8')).replace(/\r\n?/g, '\n');
    const { prepared, statements } = prepareTemplate(input);
    const formatted = beautify.html(prepared, options);
    const output = `${restoreTemplateStatements(formatted, statements).trimEnd()}\n`;

    if (output === input) {
        continue;
    }

    changed.push(path.relative(root, file).replaceAll('\\', '/'));

    if (mode === 'write') {
        await fs.writeFile(file, output, 'utf8');
    }
}

if (changed.length === 0) {
    console.log(`Views are formatted (${files.length} files checked).`);
    process.exit(0);
}

for (const file of changed) {
    console.log(`${mode === 'write' ? 'Formatted' : 'Needs formatting'}: ${file}`);
}

if (mode === 'check') {
    process.exit(1);
}

console.log(`Formatted ${changed.length} of ${files.length} view files.`);

async function listPhpFiles(directory) {
    const entries = await fs.readdir(directory, { withFileTypes: true });
    const files = [];

    for (const entry of entries.sort((left, right) => left.name.localeCompare(right.name))) {
        const target = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            files.push(...(await listPhpFiles(target)));
        } else if (entry.isFile() && entry.name.endsWith('.php')) {
            files.push(target);
        }
    }

    return files;
}

function resolveViewFile(file) {
    const resolved = path.resolve(file);
    const relative = path.relative(viewsRoot, resolved);

    if (relative.startsWith('..') || path.isAbsolute(relative) || path.extname(resolved).toLowerCase() !== '.php') {
        throw new Error(`Not a PHP view file: ${file}`);
    }

    return resolved;
}

function prepareTemplate(source) {
    const statements = [];
    const prepared = source.replace(
        /([ \t]*(?:\n[ \t]*)*)(<\?(?:php|=)[\s\S]*?\?>)([ \t]*(?:\n[ \t]*)*)/g,
        (match, leading, tag) => {
            const body = tag
                .replace(/^<\?(?:php|=)/, '')
                .replace(/\?>$/, '')
                .trim();
            const isControlStart = /^(?:if|foreach|for|while|switch)\b[\s\S]*:\s*$/.test(body);
            const isControlBranch = /^(?:else\s*:|elseif\b[\s\S]*:\s*)$/.test(body);
            const isControlEnd = /^end(?:if|foreach|for|while|switch)\s*;?$/.test(body);
            const isLayoutStatement = /^\$this->(?:extend|setData|section|endSection)\b/.test(body);
            const isCsrfField = /^csrf_field\(\)$/.test(body);

            if (!isControlStart && !isControlBranch && !isControlEnd && !isLayoutStatement && !isCsrfField) {
                return match;
            }

            const marker = `<!--PHP_TEMPLATE_STATEMENT_${statements.length}-->`;
            statements.push(tag.trim());

            if (isControlStart) {
                return `\n${marker}\n<php-control>\n`;
            }

            if (isControlBranch) {
                return `\n</php-control>\n${marker}\n<php-control>\n`;
            }

            if (isControlEnd) {
                return `\n</php-control>\n${marker}\n`;
            }

            return `\n${marker}\n`;
        },
    );

    return { prepared, statements };
}

function restoreTemplateStatements(source, statements) {
    return source
        .replace(/^[ \t]*<\/?php-control>[ \t]*(?:\n|$)/gm, '')
        .replace(/<!--PHP_TEMPLATE_STATEMENT_(\d+)-->/g, (marker, index) => statements[Number(index)] ?? marker);
}
