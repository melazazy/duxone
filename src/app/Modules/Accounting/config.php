<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Accounting Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings for the Accounting module.
    | You can define module-specific settings here, such as default values,
    | feature flags, or integration keys.
    |
    */

    /**
     * Default currency for all financial transactions.
     * Follows ISO 4217 currency codes.
     */
    'currency' => env('ACCOUNTING_CURRENCY', 'SAR'),

    /**
     * Default tax rate applied to invoices and expenses.
     * Stored as a percentage (e.g., 15 for 15%).
     */
    'default_tax_rate' => env('ACCOUNTING_DEFAULT_TAX_RATE', 15),

];

