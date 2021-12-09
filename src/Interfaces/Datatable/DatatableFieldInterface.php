<?php

namespace App\Interfaces\Datatable;

use App\Service\Datatable\DatatableServices;
use Omines\DataTablesBundle\DataTable;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 */
interface DatatableFieldInterface
{
    /**
     * @param DataTable $table
     *
     * @return DatatableServices
     */
    public function addCreatedBy(DataTable $table): self;

    /**
     * @param DataTable $table
     * @param string $template
     * @param null|array $options
     *
     * @return DataTable
     */
    public function addDeleteField(DataTable $table, string $template, ?array $options = []): DataTable;

    /**
     * @param DataTable $table
     * @param $class
     */
    public function createDatatableAdapter(DataTable $table, $class): void;
}