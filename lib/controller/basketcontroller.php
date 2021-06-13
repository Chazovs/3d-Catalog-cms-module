<?php

namespace Chazov\Unimarket\Controller;

use Bitrix\Catalog\Product\Basket;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\Sale\Fuser;
use \Bitrix\Sale\Basket as SaleBasket;
use Chazov\Unimarket\Component\Logger\Logger;
use Chazov\Unimarket\Component\Logger\LogLevel;
use Throwable;

/**
 * Class CatalogController
 * @package Chazov\Unimarket\Controller
 */
class BasketController extends Controller
{
    /**
     * @param int $productId
     * @param int $quantity
     */
    public function addToBasketAction(int $productId, int $quantity): void
    {
        try {
            global $uniContainer;

            /** @var Logger $logger */
            $logger = $uniContainer->get(Loader::class);

            $basket = SaleBasket::LoadItemsForFUser(
                Fuser::getId(),
                SITE_ID
            );

            $result = Basket::addProductToBasket(
                $basket,
                ['PRODUCT_ID' => $productId, 'QUANTITY' => $quantity],
                ['SITE_ID' => SITE_ID]
            );

            if (!$result->isSuccess()) {
                $logger->log(LogLevel::ERROR, serialize($result->getErrorMessages()));
            }

            $basket->save();

        } catch (Throwable $exception) {
            $logger->log(LogLevel::ERROR, $exception->getMessage());
        }
    }
}