<?php

namespace Rpungello\WeightedTable;

use HtmlObject\Element;
use Stringable;

class SimpleWeightedTable implements Stringable
{
    protected array $rows = [];

    protected int $numberOfColumns = 0;

    public function __construct(array $rows = [], protected array $weights = [], protected int $numberOfHeaderRows = 1)
    {
        array_walk($rows, fn ($row) => $this->addRow($row));
    }

    public function addRow(array $row): void
    {
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

        foreach ($this->getHeaderRows() as $row) {
            $thead->appendChild($tr = new Element('tr'));
            $columnIndex = 0;
            foreach ($row as $column) {
                $weight = $this->getColumnWeight($row, $columnIndex++);

                if ($weight > 1) {
                    $tr->appendChild(new Element('th', $column, ['colspan' => $weight]));
                } else {
                    $tr->appendChild(new Element('th', $column));
                }
            }
        }

        return $thead;
    }

    protected function getBodyElement(): Element
    {
        $tbody = new Element('tbody');

        foreach ($this->getBodyRows() as $row) {
            $tbody->appendChild($tr = new Element('tr'));
            $columnIndex = 0;
            foreach ($row as $column) {
                $weight = $this->getColumnWeight($row, $columnIndex++);

                if ($weight > 1) {
                    $tr->appendChild(new Element('td', $column, ['colspan' => $weight]));
                } else {
                    $tr->appendChild(new Element('td', $column));
                }
            }
        }

        return $tbody;
    }

    protected function getColumnWeight(array $row, int $columnIndex): int
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

    protected function getTotalRowWeight(array $row): int
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
