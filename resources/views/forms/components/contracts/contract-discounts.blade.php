<div>
    <table class="text-sm w-full table-auto">
        <thead>
            <tr>
                <th class="text-left">{{__('Unit price')}}</th>
                <th class="text-left">{{__('Quantity')}}</th>
                <th class="text-left">{{__('Total')}}</th>
                <th class="text-left">{{__('Discount')}}</th>
                <th class="text-left">{{__('Contract price')}}</th>
                <th class="text-left">{{__('Saving')}}</th>
            </tr>
        </thead>
        @foreach($discounts as $discount)
            <tr>
                <td>{{ formatMoney($discount['unit_price']) }}</td>
                <td>{{ $discount['quantity'] }}</td>
                <td>{{ formatMoney($discount['total']) }}</td>
                <td>{{ $discount['percentage'] }}%</td>
                <td>{{ formatMoney($discount['contract_price']) }}</td>
                <td>{{ formatMoney($discount['saving']) }}</td>
            </tr>
        @endforeach
    </table>
</div>
