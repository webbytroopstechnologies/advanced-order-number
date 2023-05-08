<?php

namespace WebbyTroops\AdvancedOrderNumber\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Sales\Models\OrderAddress;
use Webkul\Ui\DataGrid\DataGrid;

class OrderRefundDataGrid extends \Webkul\Admin\DataGrids\OrderRefundDataGrid
{
    public function prepareQueryBuilder()
    {
        $dbPrefix = DB::getTablePrefix();
        $queryBuilder = DB::table('refunds')
            ->select('refunds.id', 'refunds.increment_id as refund_increment_id', 'orders.increment_id', 'refunds.state', 'refunds.base_grand_total', 'refunds.created_at')
            ->selectRaw("CASE WHEN {$dbPrefix}refunds.increment_id IS NOT NULL THEN {$dbPrefix}refunds.increment_id ELSE {$dbPrefix}refunds.id END AS refund_increment_id")
            ->leftJoin('orders', 'refunds.order_id', '=', 'orders.id')
            ->leftJoin('addresses as order_address_billing', function($leftJoin) {
                $leftJoin->on('order_address_billing.order_id', '=', 'orders.id')
                         ->where('order_address_billing.address_type', OrderAddress::ADDRESS_TYPE_BILLING);
            })
            ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name) as billed_to'));

        $this->addFilter('billed_to', DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name)'));
        $this->addFilter('id', 'refunds.id');
        $this->addFilter('increment_id', 'orders.increment_id');
        $this->addFilter('state', 'refunds.state');
        $this->addFilter('base_grand_total', 'refunds.base_grand_total');
        $this->addFilter('created_at', 'refunds.created_at');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'refund_increment_id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'increment_id',
            'label'      => trans('admin::app.datagrid.order-id'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('admin::app.datagrid.refunded'),
            'type'       => 'price',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'billed_to',
            'label'      => trans('admin::app.datagrid.billed-to'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.refund-date'),
            'type'       => 'datetime',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

}