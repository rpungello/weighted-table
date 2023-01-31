<?php

namespace Rpungello\WeightedTable;

use HtmlObject\Element;
use Stringable;

class SimpleWeightedTable implements Stringable
{
    protected array $rows = [];

    protected int $numberOfColumns = 0;

    public function __construct(array $rows = [], protected array $weights = [], protected int $numberOfHeaderRows = 1, protected bool $alternateRowColoring = true)
    {
        array_walk($rows, fn ($row) => $this->addRow($row));
    }

    public function addRow(Row|array $row): void
    {
        if (is_array($row)) {
            $row = new Row($row);
        }

        $this->rows[] = $row;
        $this->numberOfColumns = max($this->numberOfColumns, count($row));
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function setWeights(array $weights): void
    {
        $this->weights = $weights;
    }

    public function getTotalWeight(): int
    {
        $total = array_sum($this->weights);

        return $total + max(0, $this->getNumberOfColumns() - count($this->weights));
    }

    public function getNumberOfColumns(): int
    {
        return $this->numberOfColumns;
    }

    public function getElement(): Element
    {
        $table = new Element('table');

        $table->appendChild($this->getHeadElement());
        $table->appendChild($this->getBodyElement());

        return $table;
    }

    protected function getHeadElement(): Element
    {
        $thead = new Element('thead');

        $rowIndex = 1;
        /** @var Row $row */
        foreach ($this->getHeaderRows() as $row) {
            $thead->appendChild($this->getElementFromRow($row, $rowIndex++, 'th'));
        }

        return $thead;
    }

    protected function getBodyElement(): Element
    {
        $tbody = new Element('tbody');

        $rowIndex = 1;
        /** @var Row $row */
        foreach ($this->getBodyRows() as $row) {
            $tbody->appendChild($this->getElementFromRow($row, $rowIndex++));
        }

        return $tbody;
    }

    protected function getElementFromRow(Row $row, int $rowIndex, string $cellTag = 'td'): Element
    {
        $rowAttributes = [];

        if ($row->hasClass()) {
            $rowAttributes['class'] = $row->getClass();
        } elseif ($this->alternateRowColoring && $cellTag === 'td') {
            if ($rowIndex % 2 === 0) {
                $rowAttributes['class'] = 'even';
            } else {
                $rowAttributes['class'] = 'odd';
            }
        }

        $tr = new Element('tr', attributes: $rowAttributes);

        $columnIndex = 0;
        foreach ($row->getCells() as $cell) {
            $weight = $this->getColumnWeight($row, $columnIndex++);

            if ($weight > 1) {
                $tr->appendChild(new Element($cellTag, $cell, ['colspan' => $weight]));
            } else {
                $tr->appendChild(new Element($cellTag, $cell));
            }
        }

        return $tr;
    }

    protected function getColumnWeight(Row $row, int $columnIndex): int
    {
        if ($columnIndex >= count($this->weights)) {
            $weight = 1;
        } else {
            $weight = $this->weights[$columnIndex];
        }

        if ($columnIndex === count($row) - 1 && count($row) < $this->getNumberOfColumns()) {
            $weight += $this->getTotalWeight() - $this->getTotalRowWeight($row);
        }

        return $weight;
    }

    protected function getTotalRowWeight(Row $row): int
    {
        $weight = 0;

        for ($columnIndex = 0; $columnIndex < count($row); $columnIndex++) {
            if ($columnIndex >= count($this->weights)) {
                $weight++;
            } else {
                $weight += $this->weights[$columnIndex];
            }
        }

        return $weight;
    }

    public function getHeaderRows(): array
    {
        return array_slice($this->rows, 0, $this->numberOfHeaderRows);
    }

    public function getBodyRows(): array
    {
        return array_slice($this->rows, $this->numberOfHeaderRows);
    }

    public function __toString(): string
    {
        return $this->getElement()->render();
    }
}
