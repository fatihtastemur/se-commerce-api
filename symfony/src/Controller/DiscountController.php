<?php

namespace App\Controller;

use App\Controller\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Orders;
use App\Entity\OrderProducts;
use App\Entity\Products;
use App\Entity\DiscountRules;
use App\Helper\ApiHelper;
use App\Constants\GeneralConstants;
use Throwable;

class DiscountController extends ApiController
{
    /**
     * @Route("/api/v1/discount-order/{id}", methods={"GET"})
     */
    public function calculateOrderDiscount(int $id): Response
    {
        $order = $this->entityManager->getRepository(Orders::class)->findOneBy(['id' => $id]);

        if($order) {
            $discounts = [];
            $totalDiscount = 0;
            $discountedTotal = $order->getTotalPrice();

            $discountRules = $this->entityManager->getRepository(DiscountRules::class)
                ->getDiscountRules();

            foreach ($discountRules as $discountRule) {
                switch ($discountRule['rule_type']) {
                    case 'total_price':
                        list($discountItem, $totalDiscount, $discountedTotal) = $this->calculateDiscountOverTotalPrice($discountRule, $totalDiscount, $discountedTotal);
                        if($discountItem && is_array($discountItem)) {
                            $discounts[] = $discountItem;
                        }
                        break;
                    case 'category':
                        switch ($discountRule['discount_type']) {
                            case 'free':
                                list($discountItems, $totalDiscount, $discountedTotal) = $this->calculateDiscountCategoryFree($discountRule, $totalDiscount, $discountedTotal, $id);
                                foreach ($discountItems as $discountItem) {
                                    $discounts[] = $discountItem;
                                }
                                break;
                            case 'percent':
                                list($discountItem, $totalDiscount, $discountedTotal) = $this->calculateDiscountCategoryPercent($discountRule, $totalDiscount, $discountedTotal, $id);
                                if($discountItem && is_array($discountItem)) {
                                    $discounts[] = $discountItem;
                                }
                                break;
                            default:
                        }
                        break;
                    default:
                }
            }

            // set order discount amount
            $orderDiscountAmount = $order->getDiscountAmount();
            if(empty($orderDiscountAmount)) {
                $order->setDiscountAmount($totalDiscount);
                $this->entityManager->flush();
            }

            $result = [
                'orderId' => $id,
                'discounts' => $discounts,
                'totalDiscount' => ApiHelper::numberFormater($totalDiscount),
                'discountedTotal' => ApiHelper::numberFormater($discountedTotal)
            ];

            $response = $this->response($result, 200, null);
        } else {
            $response = $this->response([], 404, "Order Not Found!");
        }

        return $response;
    }

    /**
     * @param array $rule
     * @param $totalDiscount
     * @param $discountedTotal
     * @return array
     */
    protected function calculateDiscountOverTotalPrice(array $rule, $totalDiscount, $discountedTotal): array
    {
        if(intval($rule['must']) <= $discountedTotal) {

            $discountAmount = 0;
            if($rule['discount_type'] == 'percent') {
                $discountAmount = ($discountedTotal * $rule['discount']) / 100;
                $discountAmount = (float) ApiHelper::numberFormater($discountAmount);
            }

            $totalDiscount += $discountAmount;
            $discountedTotal = $discountedTotal - $totalDiscount;

            $discountItem = [
                'discountReason' => $rule['description'],
                'discountAmount' => $discountAmount,
                'subtotal' => $discountedTotal
            ];

            $result = [$discountItem, $totalDiscount , $discountedTotal];
        } else {
            $result = [NULL, $totalDiscount , $discountedTotal];
        }

        return $result;
    }

    /**
     * @param array $rule
     * @param $totalDiscount
     * @param $discountedTotal
     * @param int $orderId
     * @return array
     */
    protected function calculateDiscountCategoryFree(array $rule, $totalDiscount, $discountedTotal, int $orderId): array
    {
        $ruleCategory = $rule['category'];

        $orderProducts = $this->entityManager->getRepository(OrderProducts::class)
            ->getOrderProductsByOrderAndCategoryId($orderId, $ruleCategory);

        $discountItems = [];
        foreach ($orderProducts as $orderProduct) {
            if($orderProduct['quantity'] >= $rule['must']) {
                $totalDiscount += ($orderProduct['unit_price'] * $rule['discount']);
                $discountedTotal -= ($orderProduct['unit_price'] * $rule['discount']);

                $discountItems[] = [
                    'discountReason' => $rule['description'],
                    'discountAmount' => ($orderProduct['unit_price'] * $rule['discount']),
                    'subtotal' => $discountedTotal
                ];
            }
        }

        return [$discountItems, $totalDiscount , $discountedTotal];
    }

    /**
     * @param array $rule
     * @param $totalDiscount
     * @param $discountedTotal
     * @param int $orderId
     * @return array
     */
    protected function calculateDiscountCategoryPercent(array $rule, $totalDiscount, $discountedTotal, int $orderId): array
    {
        $ruleCategory = $rule['category'];

        $orderProducts = $this->entityManager->getRepository(OrderProducts::class)
            ->getOrderProductsByOrderAndCategoryId($orderId, $ruleCategory);

        if($orderProducts) {
            $keys = array_column($orderProducts, 'unit_price');
            array_multisort($keys, SORT_ASC, $orderProducts);

            $minPriceProduct = $orderProducts[0];
            //$maxPriceProduct = end($orderProducts);

            $discountItem = [];
            if(count($orderProducts) >= $rule['must']) {
                if($rule['restraint']) {
                    if($rule['restraint'] == 'MIN_PRICE_PRODUCT') {
                        $totalDiscount += (($minPriceProduct['unit_price'] * $rule['discount']) / 100);
                        $discountedTotal -= (($minPriceProduct['unit_price'] * $rule['discount']) / 100);

                        $discountItem = [
                            'discountReason' => $rule['description'],
                            'discountAmount' => (($minPriceProduct['unit_price'] * $rule['discount']) / 100),
                            'subtotal' => $discountedTotal
                        ];
                    }
                    //else if restraint == MAX_PRICE_PRODUCT
                } else{
                    //can be updated according to rules that can be added later
                }
            }

            $result = [$discountItem, $totalDiscount , $discountedTotal];
        } else {
            $result = [NULL, $totalDiscount , $discountedTotal];
        }

        return $result;
    }
}
