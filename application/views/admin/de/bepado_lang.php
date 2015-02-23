<?php

// -------------------------------
// RESOURCE IDENTIFIER = STRING
// -------------------------------

use Bepado\SDK\Struct\Order;

$aLang = array(
    'charset'                          => 'UTF-8',
    'SHOP_MODULE_sBepadoLocalEndpoint' => 'Bepado apiEndpointUrl',
    'SHOP_MODULE_sBepadoApiKey'        => 'Bepado apiKey',
    'SHOP_MODULE_GROUP_main'           => 'Allgemeine Parameter',
    'SHOP_MODULE_sandboxMode'          => 'Modul im Sandbox-Modus',
    'SHOP_MODULE_sPurchaseGroupChar'   => 'Händlerpreis',
    'SHOP_MODULE_sPurchaseGroupChar_A' => 'Preis A',
    'SHOP_MODULE_sPurchaseGroupChar_B' => 'Preis B',
    'SHOP_MODULE_sPurchaseGroupChar_C' => 'Preis C',
    'api_key_not_verified'             => 'API-Key konnte nicht verifiziert werden und wurde nicht gespeichert.',
    'ARTICLE_MAIN_BEPADO'              => 'Artikel zum Export freigeben',
    'BEPADO_SETTINGS'                  => 'Einstellungen für Bepado',
    'HELP_ARTICLE_BEPADO_SETTINGS'     => 'Hier können Sie dieses Produkt zum Export nach Bepado freigeben.',

    'BEPADO_PAYMENT_TYPE'              => 'Bepado Zahlungsart',
    'HELP_BEPADO_PAYMENT_TYPE'         => 'Bitte wählen Sie eine Zahlungsart derer diese in Bepado entspricht',

    'BEPADO_PAYMENT_TYPE_'.Order::PAYMENT_ADVANCE      => 'Advanced',
    'BEPADO_PAYMENT_TYPE_'.Order::PAYMENT_INVOICE      => 'Rechnung',
    'BEPADO_PAYMENT_TYPE_'.Order::PAYMENT_DEBIT        => 'Lastschriftverfahren',
    'BEPADO_PAYMENT_TYPE_'.Order::PAYMENT_CREDITCARD   => 'Kreditkarte',
    'BEPADO_PAYMENT_TYPE_'.Order::PAYMENT_PROVIDER     => 'Provider',
    'BEPADO_PAYMENT_TYPE_'.Order::PAYMENT_OTHER        => 'Sonstiges',
    'BEPADO_PAYMENT_TYPE_'.Order::PAYMENT_UNKNOWN      => 'Unbekannt',

    'BEPADO_CATEGORY'                  => 'Bepado-Kategorie',
    'HELP_BEPADO_CATEGORY'             => 'Hier stellen Sie ein, welche Bepado-Kategorie Ihrer Shop-Kategorie entspricht.',
    'BEPADO_CATEGORY_SELECT'           => '-- keine --',
);