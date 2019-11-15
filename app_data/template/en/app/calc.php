<?php
    // This file is a PHP Template file.
    // See additional comments in [header.php].
?>

<h1><?= $app->escape($page_title) ?></h1>

<section class="calc">
    <input id="value-x" placeholder="Value X" size="7" value="<?= $app->escape($x) ?>">
    <select>
        <option value="+">Add (+)</option>
        <option value="-">Subtract (-)</option>
        <option value="*">Multiply (*)</option>
        <option value="/">Divide (/)</option>
    </select>
    <input id="value-y" placeholder="Value Y" size="7" value="<?= $app->escape($y) ?>">
    <button>Calculate</button>
</section>

<section class="calc-result" style="display:none;">
    <ul>
    </ul>
</section>

<script src="calc.js"></script>
