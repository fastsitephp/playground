<?php
    // Este é um arquivo Modelo PHP
    // Veja comentários adicionais em [header.php].
?>

<h1><?= $app->escape($page_title) ?></h1>

<section class="calc">
    <input id="value-x" placeholder="Value X" size="7" value="<?= $app->escape($x) ?>">
    <select>
        <option value="+">Adicionar (+)</option>
        <option value="-">Subtrair (-)</option>
        <option value="*">Multiplicar (*)</option>
        <option value="/">Dividir (/)</option>
    </select>
    <input id="value-y" placeholder="Value Y" size="7" value="<?= $app->escape($y) ?>">
    <button>Calcular</button>
</section>

<section class="calc-result" style="display:none;">
    <ul>
    </ul>
</section>

<script src="calc.js"></script>
