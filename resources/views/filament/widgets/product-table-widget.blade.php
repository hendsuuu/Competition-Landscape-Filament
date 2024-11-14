<div class="card bg-white dark:bg-gray-900 shadow-md rounded-lg overflow-hidden w-full">
    <div class="card-body p-4">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-200 dark:border-gray-700 min-w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Denom / Brand
                        </th>
                        @foreach ($brands as $brand)
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">
                            {{ $brand['brand_name'] }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($denoms as $denom)
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-900">
                            <!-- Kolom pertama sebagai header untuk setiap row -->
                            {{ $denom->denom }}
                        </th>
                        @foreach ($brands as $brand)
                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                            {!! $this->tableData[$denom->denom][$brand['brand_name']] !!}
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>