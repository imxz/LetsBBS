import { spawnSync } from 'node:child_process';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const target = path.resolve(process.argv[2] ?? '');
const relative = path.relative(root, target);

if (!process.argv[2] || relative.startsWith('..') || path.isAbsolute(relative) || path.extname(target) !== '.php') {
    console.error('Usage: node tools/format-file.mjs <workspace PHP file>');
    process.exit(2);
}

if (isInside(path.join(root, 'app', 'Views'), target)) {
    run(process.execPath, [path.join(root, 'tools', 'format-views.mjs'), '--write', target]);
} else {
    run(process.execPath, [path.join(root, 'node_modules', 'prettier', 'bin', 'prettier.cjs'), '--write', target]);
}

run('php', [
    path.join(root, 'vendor', 'bin', 'php-cs-fixer'),
    'fix',
    '--config=.php-cs-fixer.dist.php',
    '--path-mode=intersection',
    '--using-cache=no',
    '--show-progress=none',
    target,
]);

function isInside(directory, file) {
    const child = path.relative(directory, file);
    return child !== '' && !child.startsWith('..') && !path.isAbsolute(child);
}

function run(command, args) {
    const result = spawnSync(command, args, {
        cwd: root,
        env: process.env,
        stdio: 'inherit',
        windowsHide: true,
    });

    if (result.error) {
        console.error(result.error.message);
        process.exit(1);
    }

    if (result.status !== 0) {
        process.exit(result.status ?? 1);
    }
}
