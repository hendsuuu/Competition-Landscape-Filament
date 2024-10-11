<div class="card bg-white shadow-md rounded-lg overflow-hidden w-full">
    <div class="card-body p-4">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-200 min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Denom / Brand</th>
                        @foreach ($brands as $brand)
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">{{ $brand['brand_name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($denoms as $denom)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <th class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100"> <!-- Jadikan kolom pertama header -->
                            {{ $denom->denom }}
                        </th>
                        @foreach ($brands as $brand)
                        <td class="px-4 py-2 text-sm text-gray-700">{!! $this->tableData[$denom->denom][$brand['brand_name']] !!}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>