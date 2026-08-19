<?php
$children = [];
$roots = [];
$standalone = [];

foreach ($directoryNodes as $node) {
    if ($node['parent_id']) {
        $children[(int) $node['parent_id']][] = $node;
    } else {
        $roots[] = $node;
    }
}
?>

<?php foreach ($roots as $root):?>

    <?php if (!empty($children[(int) $root['id']])):?>
        <div class="row node-directory-row">
            <div class="col-sm-2 node-category"><?= esc($root['name']) ?></div>
            <div class="col-sm-10 node-links">
                <?php foreach ($children[(int) $root['id']] as $child):?>
                    <a class="btn btn-outline-secondary"
                        href="/node/<?= (int) $child['id'] ?>"><?= esc($child['name']) ?></a>
                <?php endforeach?>
            </div>
        </div>
    <?php else:?>
        <?php $standalone[] = $root; ?>

    <?php endif?>

<?php endforeach?>

<?php if ($standalone):?>
    <div class="row node-directory-row">
        <div class="offset-sm-2 col-sm-10 node-links">
            <?php foreach ($standalone as $node):?>
                <a class="btn btn-outline-secondary" href="/node/<?= (int) $node['id'] ?>"><?= esc($node['name']) ?></a>
            <?php endforeach?>
        </div>
    </div>
<?php endif?>
