<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Denom / Brand</th>
                    @foreach ($brands as $brand)
                    <th>{{ $brand->brand_id }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($denoms as $denom)
                <tr>
                    <td>{{ $denom->denom }}</td>
                    @foreach ($brands as $brand)
                    <td>{{ $tableData[$denom->denom][$brand->brand_id] ?? 'N/A' }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>