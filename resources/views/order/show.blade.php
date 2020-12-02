@extends('shopify-app::layouts.default')

@section('styles')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
@endsection
	
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

@section('content')
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<i class="fa fa-align-justify">Order ID: {{$record->order_id}}</i>
			</div>
			<div class="form-actions col-md-2">
        		<a href="{{ route('home_menu') }}" class="btn btn-sm btn-secondary">< Home</a>
        		<span></span>
        		<a href="{{ route('order_index') }}" class="btn btn-sm btn-secondary">< Order List</a>
        	</div>
			<div class="card-body">
				<table class="table table-responsive-sm table-striped">
					<thead>
						<tr>
							<th>Order</th>
    						<th>Date</th>
    						<th>Customer</th>
    						<th>Total</th>
    						<th>Payment</th>
    						<th>Fulfillment</th>
    						<th>Items</th>
    						<th>Tags</th>
						</tr>
					</thead>
					<thead>
						<tr>
							<th>{{ $record->order_name }}</th>
    						<th>{{ $record->created_at }}</th>
    						<th>{{ $record->email }}</th>
    						<th>${{ $record->total_price }}</th>
    						<th>{{ $record->financial_status }}</th>
    						<th>{{ $record->fulfillment_status ?: 'Unfulfilled' }}</th>
    						<th>{{ $item_count }}</th>
    						<th>{{ $record->tags }}</th>
						</tr>
					</thead>
				</table>
			</div>
			
			<div class="card-header">
				Items
			</div>
			<div class="card-body">
				<table class="table table-responsive-sm table-striped">
					<thead>
						<tr>
							<th>Id</th>
							<th>Name</th>
    						<th>Product</th>
    						<th>Price</th>
    						<th>Quantity</th>
    						<th>SKU</th>
    						<th>Fulfillment Status</th>
						</tr>
					</thead><?php //print_r(count($line_items));die();?>
					<thead>
						@foreach($lineitems as $line_item)
						<tr>
							<th>{{ $line_item['lineitem_id'] }}</th>
							<th>{{ $line_item['variant_name'] }}</th>
    						<th>{{ $line_item['product_id'] }}</th>
    						<th>${{ $line_item['price'] }}</th>
    						<th>{{ $line_item['quantity'] }}</th>
    						<th>{{ $line_item['sku'] }}</th>
    						<th>{{ $line_item['fulfillment_status'] }}</th>
						</tr>
						@endforeach
					</thead>
				</table>
			</div>

			@foreach($products as $product)
    			<div class="card-header">
    				Products: {{ $product->id }}
    			</div>
    			<div class="card-body">
    				<table class="table table-responsive-sm table-striped">
    					<thead>
    						<tr>
    							<th>Name</th>
        						<th>Tags</th>
        						<th>Status</th>
        						<th>Variants Count</th>
    						</tr>
    					</thead>
    					<thead>
    						<tr>
    							<th>{{ $product->title }}</th>
    							<th>{{ $product->tags ?: 'null' }}</th>
        						<th>{{ $product->status }}</th>
        						<th>{{ count($product->variants) }}</th>
    						</tr>
    						
    					</thead>
    				</table>
    			</div>
    			@if(count($product->variants) > 0)
        			@foreach($product->variants as $variant)
        				<div class="card-body">
        					Variants: {{ $variant->id }}
            				<table class="table table-responsive-sm table-striped">
            					<thead>
            						<tr>
            							<th>Name</th>
                						<th>Price</th>
                						<th>SKU</th>
            						</tr>
            					</thead>
            					<thead>
            						<tr>
            							<th>{{ $variant->title }}</th>
            							<th>{{ $variant->price }}</th>
                						<th>{{ $variant->sku }}</th>
            						</tr>
            						
            					</thead>
            				</table>
            			</div>
    				@endforeach
    			@endif
			
			@endforeach
		</div>
	</div>
@endsection

@section('scripts')
    @parent

    @if(config('shopify-app.appbridge_enabled'))
        <script type="text/javascript">
            var AppBridge = window['app-bridge'];
            var actions = AppBridge.actions;
            var TitleBar = actions.TitleBar;
            var Button = actions.Button;
            var Redirect = actions.Redirect;
            var titleBarOptions = {
                title: 'Welcome',
            };
            var myTitleBar = TitleBar.create(app, titleBarOptions);
        </script>
    @endif
@endsection