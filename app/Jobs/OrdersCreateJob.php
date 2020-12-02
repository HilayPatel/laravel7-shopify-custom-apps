<?php namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use stdClass;

use App\Http\Controllers\ShopifyOrderController;

class OrdersCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param ShopDomain $shopDomain The shop's myshopify domain
     * @param stdClass   $data       The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @author Kay Liong
     * @return void
     */
    public function handle()
    {
        // Do what you wish with the data
        // Access domain name as $this->shopDomain->toNative()
        // send to controller for order model insert/update
        
        try{
            // convert object to array
            $data_arr = json_decode(json_encode($this->data), true);
            
            $order = new ShopifyOrderController();
            $order->createNewOrder($data_arr);
            
        } catch (\Exception $e) {
            // TODO: temp log error this way, for debugging
            file_put_contents("/var/log/webhook_error.log", "error webhook create order:  ".$e->getMessage() ."\n",FILE_APPEND);
        }
    }
}
