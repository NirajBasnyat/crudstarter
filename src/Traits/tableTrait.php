<?php


namespace Niraj\CrudStarter\Traits;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

trait tableTrait
{
    protected $tArray;

    public function __construct($tArray)
    {
        $this->tArray = $tArray;
    }

    protected function setUpTable($message = 'Done'): void
    {
        $seperator = new TableSeparator;
        $this->tArray[] = $seperator;
        $this->tArray[] = [new TableCell($message. ' Successfully !', ['colspan' => 2])];
    }

    protected function showTableInfo($arr, $message): void
    {
        $this->tArray = $arr;

        $this->setUpTable($message);

        // Create a new Table instance.
        $table = new Table($this->output);

        // Set the table headers.
        $table->setHeaders([
            'File Type', 'Status'
        ]);

        // Set the contents of the table.
        $table->setRows(
            $this->tArray
        );

        // Render the table to the output.
        $table->render();
    }
}