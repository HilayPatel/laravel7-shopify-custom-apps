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
				<i class="fa fa-align-justify">Order List</i>
			</div>
			<div class="form-actions col-md-2">
        		<a href="{{ route('home_menu') }}" class="btn btn-sm btn-secondary">< Home</a>
        		<span></span>
        		<a href="{{ route('order_table_export') }}" class="btn btn-sm btn-info">Export XLSX ></a>
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
						@foreach($records as $record)
						<tr>
							<th><a href="{{ route('search_order_name', $record->id) }}"> {{ $record->order_name }}</th>
    						<th>{{ $record->created_at }}</th>
    						<th>{{ $record->email }}</th>
    						<th>{{ $record->total_price }}</th>
    						<th>{{ $record->financial_status }}</th>
    						<th>{{ $record->fulfillment_status ?: 'Unfulfilled' }}</th>
    						<th>{{ $record->item_count }}</th>
    						<th>{{ $record->tags }}</th>
						</tr>
						@endforeach
					</thead>
				</table>
			</div>
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