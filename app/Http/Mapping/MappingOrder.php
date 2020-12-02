<?php
namespace App\Http\Mapping;

class Mapping_Order extends Mapping_Base
{
    protected $order;
    protected $lineItems;
    protected $fulfillments;

    public function __construct()
    {
    }

    /**
     * @param boolean $db DB ready array
     * @return mixed
     */
    public function getOrder($db = false)
    {
        if ($db) {
            return $this->cleanArray($this->order);
        }
        return $this->order;
    }

    /**
     * @param boolean $db DB ready array
     * @return mixed
     */
    public function getLineItems($db = false)
    {

        if ($db) {
            return $this->cleanBulkArray($this->lineItems);
        }
        return $this->lineItems;
    }

    /**
     * @param boolean $db DB ready array
     * @return mixed
     */
    public function getFulfillments($db = false)
    {
        if ($db) {
            return $this->cleanBulkArray($this->fulfillments);
        }
        return $this->fulfillments;
    }

    /**
     * Pass the json object in and it will map it into an array
     * @param $json object order json object
     * @param $countryCode string country code
     * @return $this
     */
    public function mapOrder($json, $countryCode)
    {
        // map the order

        $order = [
            'country_code' => 'SG',
            'order_id' => $this->getValue($json, ['id']),
            'email' => $this->getValue($json, ['email']),
            'created_at' => $this->getValue($json, ['created_at']),
            'updated_at' => $this->getValue($json, ['updated_at']),
            'total_price' => $this->getValue($json, ['total_price']),
            'sub_total' => $this->getValue($json, ['subtotal_price']),
            'total_tax' => $this->getValue($json, ['total_tax']),
            'currency' => $this->getValue($json, ['currency']),
            'total_discounts' => $this->getValue($json, ['total_discounts']),
            'order_name' => $this->getValue($json, ['name']),
            'cancelled_at' => date("Y-m-d H:i:s", strtotime($this->getValue($json, ['cancelled_at'])) ),
            'phone' => $this->getValue($json, ['phone']),
            'discount_codes' => $this->getValue($json, ['discount_codes'], []),
            'note' => $this->getValue($json, ['note']),
            'source_name' => $this->getValue($json, ['source_name']),
            'checkout_id' => $this->getValue($json, ['checkout_id']),
            'fulfillment_status' => $this->getValue($json, ['fulfillment_status']),
            'refunds' => $this->getValue($json, ['refunds']),
            'line_items' => $this->getValue($json, ['line_items'], []),
            'fulfillments' => $this->getValue($json, ['fulfillments'], []),
            'note_attributes' => $this->getValue($json, ['note_attributes'], ""),
        ];

        if (count($order['discount_codes']) > 0) {
            $order['discount_code'] = $this->getValue($order, ['discount_codes',0,'code']);
            $order['discount_amount'] = $this->getValue($order, ['discount_codes',0,'amount']);
        }

        if (!empty($order['note_attributes'])) {
            $search = array_filter($order['note_attributes'], function ($value) {
                return $value['name'] == 'paylah_id' || $value['name'] == 'stripe_id' || $value['name'] =='paylah';
            });

            if (!empty($search)) {
                $value = array_values($search);
                $order['checkout_id'] = $value['value'];
            }

            $order['note_attributes'] = json_encode($order['note_attributes']);
        }

        $order['line_items'] = $this->mapLineitems($order['line_items'], $order['order_id'])->getLineItems();
        $order['fulfillments'] = $this->mapFulfillments($order['fulfillments'], $order)->getFulfillments();

        $this->order = $order;

        return $this;
    }


    /**
     * @param $json object fulfillments object
     * @param array $order The original order object
     *
     * @return $this;
     */
    public function mapFulfillments($json, $order = [])
    {

        $this->fulfillments = [];
        foreach ($json as $fulfillment) {
            $lineitems = getValue($fulfillment, ['line_items']);
            foreach ($lineitems as $lineitem) {
                $fulfillmentMap = [
                    'fulfillment_id'=> $this->getValue($fulfillment, ['id']),
                    'transaction_id'=> $this->getValue($fulfillment, ['id']),
                    'created_at'=> $this->getValue($fulfillment, ['created_at']),
                    'updated_at'=> $this->getValue($fulfillment, ['updated_at']),
                    'order_id'=> $this->getValue($fulfillment, ['order_id']),
                    'status'=> $this->getValue($fulfillment, ['status']),
                    'tracking_number'=> $this->getValue($fulfillment, ['tracking_number']),
                    'lineitem_id'=> $this->getValue($lineitem, ['id']),
                    'variant_id'=> $this->getValue($lineitem, ['variant_id']),
                    'product_id'=> $this->getValue($lineitem, ['product_id']),
                    'price'=> $this->getValue($lineitem, ['price']),
                    'quantity'=> $this->getValue($lineitem, ['quantity']),
                    'sku'=> $this->getValue($lineitem, ['sku']),
                    'variant_name'=> $this->getValue($lineitem, ['name']),
                    'fulfillable_quantity'=> $this->getValue($lineitem, ['fulfillable_quantity']),
                    'fulfillment_status'=> $this->getValue($lineitem, ['fulfillment_status']),
                    'vendor'=> $this->getValue($lineitem, ['vendor']),
                    'tax_lines' => $this->getValue($lineitem, ['tax_lines']),
                    'discount_allocations' => $this->getValue($lineitem, ['discount_allocations'], []),
                ];

                if (count($fulfillmentMap['tax_lines']) > 0) {
                    $fulfillmentMap['tax_price'] = $this->getValue($lineitem, ['tax_lines', 0, 'price']);
                    $fulfillmentMap['tax_rate']  = $this->getValue($lineitem, ['tax_lines', 0, 'rate']);
                }

                //only update this during the update webhook
                if (count($fulfillmentMap['discount_allocations']) > 0 && !empty($order)) {
                    $value = $this->getValue($lineitem, ['discount_allocations', 0, 'amount']);
                    $quantity = $this->getValue($lineitem, ['quantity']);
                    $lineitemId = $this->getValue($lineitem, ['id']);

                    $originalLineitem = $this->getValue($order, ['line_items']);
                    $originalLineitem = array_filter($originalLineitem, function ($e) use ($lineitemId) {
                        return $lineitemId === $e['lineitem_id'] || $lineitemId === $e['id'];
                    });
                    $originalLineitem = array_values($originalLineitem);
                    $originalQty = getValue($originalLineitem, [0, 'quantity']);

                    $fulfillmentMap['discount_value']  = round(($value / $originalQty) * $quantity, 2);
                }

                $this->fulfillments[] = $fulfillmentMap;
            }
        }

        return $this;
    }


    /**
     * @param $json object line items object
     * @param $order_id int original order
     *
     * @return $this
     */
    public function mapLineitems($json, $order_id)
    {

        $this->lineItems = [];
        foreach ($json as $lineitem) {
            $lineitemMap = [
                'lineitem_id' => $this->getValue($lineitem, ['id']),
                'order_id' => $order_id,
                'variant_id' => $this->getValue($lineitem, ['variant_id']),
                'product_id' => $this->getValue($lineitem, ['product_id']),
                'price' => $this->getValue($lineitem, ['price']),
                'quantity' => $this->getValue($lineitem, ['quantity']),
                'sku' => $this->getValue($lineitem, ['sku'], ""),
                'variant_name' => $this->getValue($lineitem, ['name']),
                'fulfillable_quantity' => $this->getValue($lineitem, ['fulfillable_quantity']),
                'fulfillment_status' => $this->getValue($lineitem, ['fulfillment_status'], ""),
                'vendor' => $this->getValue($lineitem, ['vendor']),
                'tax_lines' => $this->getValue($lineitem, ['tax_lines'], []),
                'discount_allocations' => $this->getValue($lineitem, ['discount_allocations'], []),
            ];


            if (count($lineitemMap['tax_lines']) > 0) {
                $lineitemMap['tax_price'] = $this->getValue($lineitem, ['tax_lines', 0, 'price']);
                $lineitemMap['tax_rate']  = $this->getValue($lineitem, ['tax_lines', 0, 'rate']);
            }

            if (count($lineitemMap['discount_allocations']) > 0) {
                $lineitemMap['discount_value']  = $this->getValue($lineitem, ['discount_allocations', 0, 'amount']);
            }

            $this->lineItems[] = $lineitemMap;
        }

        return $this;
    }
}
