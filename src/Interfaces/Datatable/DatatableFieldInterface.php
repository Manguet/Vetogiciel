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
     * @param string $fieldName
     * @param string $label
     * @param string $url
     * @param string|null $authorization
     *
     * @return DataTable
     */
    public function addFieldWithEditField(DataTable $table, string $fieldName, string $label,
                                          string $url, ?string $authorization = null): DataTable;

    /**
     * @param DataTable $table
     * @param $class
     */
    public function createDatatableAdapter(DataTable $table, $class): void;
}