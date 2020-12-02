<?php
/**
 * Export to Excel
 * @author Kay
 */

namespace App\Exports;

use App\Models\ShopifyOrder;
use App\Models\ShopifyOrderFulfillment;
use App\Models\ShopifyOrderLineitem;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;

class OrderExport implements FromArray, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
//     public function collection()
//     {
//         return ShopifyOrder::all();
//     }
    
    /**
     * Using Arrays
     * {@inheritDoc}
     * @see \Maatwebsite\Excel\Concerns\FromArray::array()
     */
    public function array(): array
    {
        return $this->getOrderReport();
    }
    
    /**
     * Set the header row
     * {@inheritDoc}
     * @see \Maatwebsite\Excel\Concerns\WithHeadings::headings()
     */
    public function headings(): array
    {
        return [
            'Order',
            'Date',
            'Customer',
            'Total',
            'Payment',
            'Fulfillment',
            'Items',
            'Tags'
        ];
    }
    
    /**
     * Process the data to export
     * @return array
     */
    public function getOrderReport(){
        
        $records = ShopifyOrder::get();
        
        $report = [];
        if(!$records->isEmpty()){
            foreach($records as $key=>$record){
                if(!empty($record->order_id)){
                    
                    $report[$key][] = $record->order_name;
                    $report[$key][] = date("Y-m-d H:i:s", strtotime($record->created_at));
                    $report[$key][] = $record->email;
                    $report[$key][] = $record->total_price;
                    $report[$key][] = $record->financial_status;
                    $report[$key][] = $record->fulfillment_status ?: 'Unfulfilled';
                    
                    $line_items = ShopifyOrderLineitem::where('order_id', '=', $record->order_id)->get()->toArray();
                    
                    // process line items
                    foreach($line_items as $lk=>$line_items){
                        $lineitems[$lk] = $line_items;
                    }
                    
                    $records[$key]['item_count'] = count($lineitems) . " items";
                    if( count($lineitems) <=1 ){
                        $records[$key]['item_count'] = count($lineitems) . " item";
                    }
                    
                    $report[$key][] = $record->item_count;
                    $report[$key][] = $record->tags;
                }
            }
            file_put_contents("/var/log/webhook_error.log", "export order:  ".print_r($report, true) ."\n",FILE_APPEND);
            return $report;
            
        }
    }
}
