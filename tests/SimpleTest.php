<?php

use Rpungello\WeightedTable\Row;
use Rpungello\WeightedTable\SimpleWeightedTable;

it('can instantiate class', function () {
    $instance = new SimpleWeightedTable();
    expect($instance)->toBeInstanceOf(SimpleWeightedTable::class);
});

it('can instantiate class with rows', function () {
    $instance = new SimpleWeightedTable([['Column 1', 'Column 2']]);
    $rows = $instance->getRows();

    expect($instance->getNumberOfColumns())->toBe(2);
    expect($rows)->toHaveCount(1);
    expect($rows[0])->toHaveCount(2);
    expect($rows[0][0])->toBe('Column 1');
    expect($rows[0][1])->toBe('Column 2');
});

it('can add rows', function () {
    $instance = new SimpleWeightedTable();

    $instance->addRow(['Column 1', 'Column 2']);

    expect($instance->getNumberOfColumns())->toBe(2);

    $rows = $instance->getRows();

    expect($rows)->toHaveCount(1);
    expect($rows[0])->toHaveCount(2);
    expect($rows[0][0])->toBe('Column 1');
    expect($rows[0][1])->toBe('Column 2');
});

it('can calculate total weight', function () {
    $instance = new SimpleWeightedTable([], [1, 1, 2, 3, 5]);
    expect($instance->getTotalWeight())->toBe(12);
});

it('can compile html', function () {
    $instance = new SimpleWeightedTable([['Column 1', 'Column 2', 'Column 3'], ['Value 1', 'Value 2'], ['Value 3']], [2], 1, false);
    expect($instance->__toString())->toBe('<table><thead><tr><th colspan="2">Column 1</th><th>Column 2</th><th>Column 3</th></tr></thead><tbody><tr><td colspan="2">Value 1</td><td colspan="2">Value 2</td></tr><tr><td colspan="4">Value 3</td></tr></tbody></table>');
});

it('can compile html with alternate row coloring', function () {
    $instance = new SimpleWeightedTable([['Column 1', 'Column 2', 'Column 3'], ['Value 1', 'Value 2'], ['Value 3']], [2], alternateRowColoring: true);
    expect($instance->__toString())->toBe('<table><thead><tr><th colspan="2">Column 1</th><th>Column 2</th><th>Column 3</th></tr></thead><tbody><tr class="odd"><td colspan="2">Value 1</td><td colspan="2">Value 2</td></tr><tr class="even"><td colspan="4">Value 3</td></tr></tbody></table>');
});

it('can compile html with custom classes', function () {
    $instance = new SimpleWeightedTable();
    $instance->addRow(new Row(['Column 1', 'Column 2', 'Column 3'], 'class0'));
    $instance->addRow(new Row(['Value 1', 'Value 2'], 'class1'));
    $instance->addRow(new Row(['Value 3'], 'class2'));
    $instance->setWeights([2]);
    expect($instance->__toString())->toBe('<table><thead><tr class="class0"><th colspan="2">Column 1</th><th>Column 2</th><th>Column 3</th></tr></thead><tbody><tr class="class1"><td colspan="2">Value 1</td><td colspan="2">Value 2</td></tr><tr class="class2"><td colspan="4">Value 3</td></tr></tbody></table>');
});
