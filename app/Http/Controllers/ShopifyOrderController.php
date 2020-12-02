<?php

namespace App\Http\Controllers;

use App\Models\ShopifyOrder;
use App\Models\ShopifyOrderFulfillment;
use App\Models\ShopifyOrderLineitem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Shopify Order
 * 
 * @author kayliongmac11air
 *
 */
class ShopifyOrderController extends Controller
{
    protected $order;
    protected $lineItems;
    protected $fulfillments;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = ShopifyOrder::get();
        
        if(!$records->isEmpty()){
            foreach($records as $key=>$record){
                if(!empty($record->order_id)){
                    
                    $line_items = ShopifyOrderLineitem::where('order_id', '=', $record->order_id)->get()->toArray();
                    
                    // process line items
                    foreach($line_items as $lk=>$line_items){
                        $lineitems[$lk] = $line_items;
                    }
                    
                    $records[$key]['item_count'] = count($lineitems) . " items";
                    if( count($lineitems) <=1 ){
                        $records[$key]['item_count'] = count($lineitems) . " item";
                    }
                }
            }
            return view('order.index', compact('records', 'lineitems'));
        }
        
        return redirect("/home")->with('error', 'No order found.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShopifyOrder  $shopifyOrder
     * @return \Illuminate\Http\Response
     */
    public function show(ShopifyOrder $shopifyOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShopifyOrder  $shopifyOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(ShopifyOrder $shopifyOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShopifyOrder  $shopifyOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShopifyOrder $shopifyOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShopifyOrder  $shopifyOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShopifyOrder $shopifyOrder)
    {
        //
    }
    
    /**
     * Search by order name, #99999
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function searchByOrderName(Request $request)
    {
        $record = $line_items = $products = [];
        $shop = Auth::user();
        if(!empty($request['order_name'])){
            $record = ShopifyOrder::where('order_name', $request['order_name'])->first();
            
            if(!empty($record->order_id)){

                $line_items = ShopifyOrderLineitem::where('order_id', '=', $record->order_id)->get()->toArray();
                
                // process line items
                foreach($line_items as $key=>$line_items){
                    $products[$line_items['product_id']] = $shop->api()->rest('GET', '/admin/api/2020-10/products/'.$line_items['product_id'].'.json')['body']['product'];
                    $lineitems[$key] = $line_items;
                }
                
                $item_count = count($lineitems) . " items";
                if( count($lineitems) <=1 ){
                    $item_count = count($lineitems) . " item";
                }

                return view('order.show', compact('record', 'lineitems', 'products', 'item_count'));
            }
            
            return redirect("/home")
            ->with('error', 'No order found.');
        }
        
        return redirect("/home")
        ->with('error', 'No order found.');
    }
    
    /**
     * Get by order name, #99999
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getByOrderName(ShopifyOrder $order)
    {
        $record = $line_items = $products = [];
        $shop = Auth::user();
        if(!empty($order->order_name)){
            
            $line_items = ShopifyOrderLineitem::where('order_id', '=', $order->order_id)->get()->toArray();
                
            // process line items
            foreach($line_items as $key=>$line_items){
                $products[$line_items['product_id']] = $shop->api()->rest('GET', '/admin/api/2020-10/products/'.$line_items['product_id'].'.json')['body']['product'];
                $lineitems[$key] = $line_items;
            }
            
            $item_count = count($lineitems) . " items";
            if( count($lineitems) <=1 ){
                $item_count = count($lineitems) . " item";
            }
            
            $record = $order;
            return view('order.show', compact('record', 'lineitems', 'products', 'item_count'));
        }
        
        return redirect("/home")
        ->with('error', 'No order found.');
    }
    
    /**
     * Crete new order called by WebHook
     * 
     * @param array $order
     */
    public function createNewOrder($post = []){
        try{
            
            $formatted_order=$this->mapOrder($post);
            
            //file_put_contents("/var/log/webhook_error.log", "Formated Order :  ".print_r($this->getLineItems(), true) ."\n",FILE_APPEND);
            
            $new_order = new ShopifyOrder();
            $new_order->fill($formatted_order);
            $new_order->save();
            
            // Fullfilment
            if(!empty($this->getFulfillments())){
                $new_fulfillment = new ShopifyOrderFulfillment();
                $new_fulfillment->fill($this->getFulfillments());
                $new_fulfillment->save();
            }
            
            // line items
            
            if(!empty($lineitems = $this->getLineItems())){
                foreach ($lineitems as $item) {
                    $new_lineitems = new ShopifyOrderLineitem();
                    $new_lineitems->fill($item);
                    $new_lineitems->save();
                }
            }
            
            
            //file_put_contents("/var/log/webhook_error.log", "Formated Order :  ".print_r($this->getFulfillments(), true) ."\n",FILE_APPEND);
            
            //file_put_contents("/var/log/webhook_error.log", "error webhook create order:  ".$formattedOrder ."\n",FILE_APPEND);
            
        } catch (\Exception $e) {
            // TODO: temp log error this way, for debugging
            file_put_contents("/var/log/webhook_error.log", "error webhook create order:  ".$e->getMessage() ."\n",FILE_APPEND);
            }
    }
    
    /**
     * Pass the json object in and i will map it into an array
     * @param $json array order json object
     * @param $countryCode string country code
     * @return $this
     */
    public function mapOrder($json)
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
            'tags' => $this->getValue($json, ['tags'], ""),
            'financial_status' => $this->getValue($json, ['financial_status'], ""),
        ];
        
        if (count($order['discount_codes']) > 0) {
            $order['discount_code'] = getValue($order, ['discount_codes',0,'code']);
            $order['discount_amount'] = getValue($order, ['discount_codes',0,'amount']);
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
        else{
            $order['note_attributes'] = '';
        }
        
        $order['line_items'] = $this->mapLineitems($order['line_items'], $order['order_id']);
        $order['fulfillments'] = $this->mapFulfillments($order['fulfillments'], $order);
        
        $this->order = $order;
        
        return $order;
    }
    
    
    /**
     * @param $json array fulfillments object
     * @param array $order The original order object
     *
     * @return $this;
     */
    public function mapFulfillments($json, $order = [])
    {
        
        $this->fulfillments = [];
        foreach ($json as $fulfillment) {
            $lineitems = $this->getValue($fulfillment, ['line_items']);
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
                        $originalQty = $this->getValue($originalLineitem, [0, 'quantity']);
                        
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
            else{
                $lineitemMap['discount_value']  = 0;
            }
            
            $this->lineItems[] = $lineitemMap;
        }
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->cleanBulkArray($this->order);
    }
    
    /**
     * @return mixed
     */
    public function getLineItems()
    {
        return $this->cleanBulkArray($this->lineItems);
    }
    
    /**
     * @return mixed
     */
    public function getFulfillments()
    {
        return $this->cleanBulkArray($this->fulfillments);
    }
    
    /**
     * A helper function to check if a value isset properly if not return a default value
     * a cleaner way to do truthy  $value ? : 'default';
     * @param array|object $list
     * @param string|array $key
     * @param null $default
     * @return mixed
     */
    function getValue($list, $key, $default = null)
    {
        if (!is_array($list) && !is_object($list)) { // if the first value is not an array return default
            return $default;
        }
        
        if (!is_array($key)) {
            $key = [$key];
        }
        
        $current = $list;
        foreach ($key as $element) {
            if (is_array($current)) {
                if (!isset($current[$element])) {
                    return $default;
                }
                $current = $current[$element];
            } else {
                if (!isset($current->$element)) {
                    return $default;
                }
                $current = $current->$element;
            }
        }
        
        return $current;
    }
    
    /**
     * Remove all objects and array from an array/object
     * @param $object
     * @return array
     */
    protected function cleanArray($object)
    {
        
        $sorted = [];
        foreach ($object as $key => $value) {
            if (!is_array($value) && !is_object($value) && !is_null($value)) {
                $sorted[$key] = $value;
            }
        }
        return $sorted;
    }
    
    protected function cleanBulkArray($objects)
    {
        $sorted = [];
        $headers = [];
        foreach ($objects as $key1 => $obj) {
            foreach ($obj as $key2 => $value) {
                if (!is_array($value) && !is_object($value) && !is_null($value)) {
                    if (!in_array($key2, $headers)) {
                        $headers[] = $key2;
                    }
                    
                    $sorted[$key1][$key2] = $value;
                }
            }
        }
        //now we add in the missing columns
        foreach ($sorted as $key => $obj) {
            foreach ($headers as $header) {
                if (!key_exists($header, $obj)) {
                    $sorted[$key][$header] = "{NULL}"; // insert as null into the DB
                }
            }
        }
        
        return $sorted;
    }
    
    public function exportTablesToExcel(){
        return Excel::download(new OrderExport, 'Order.xlsx');
    }
}
