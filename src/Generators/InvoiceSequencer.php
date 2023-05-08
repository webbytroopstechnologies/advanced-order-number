<?php

namespace WebbyTroops\AdvancedOrderNumber\Generators;

use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Generators\Sequencer;
use WebbyTroops\AdvancedOrderNumber\Helpers\SequenceNumberHelper;

class InvoiceSequencer extends Sequencer
{
    /**
     * Create order sequencer instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setAllConfigs();
    }

    /**
     * Set all configs.
     *
     * @param  string  $configKey
     * @return void
     */
    public function setAllConfigs()
    {
        $this->prefix = core()->getConfigData('sales.invoice_setttings.invoice_number.invoice_number_prefix');

        $this->length = core()->getConfigData('sales.invoice_setttings.invoice_number.invoice_number_length');

        $this->suffix = core()->getConfigData('sales.invoice_setttings.invoice_number.invoice_number_suffix');
        
        $currentChannel = SequenceNumberHelper::getCurrentChannelCode();
        
        $isModuleEnabled = core()->getConfigData('sales.advancedOrderNumberSetting.general.advance-order-number-enable', $currentChannel);
        
        if($isModuleEnabled) {
            $this->generatorClass = \WebbyTroops\AdvancedOrderNumber\Generators\InvoiceNumberGenerator::class;
        } else {
            $this->generatorClass = core()->getConfigData('sales.invoice_setttings.invoice_number.invoice_number_generator_class');
        }

        $this->lastId = $this->getLastId();
    }

    /**
     * Get last id.
     *
     * @return int
     */
    public function getLastId()
    {
        $lastOrder = Invoice::query()->orderBy('id', 'desc')->limit(1)->first();

        return $lastOrder ? $lastOrder->id : 0;
    }
}
