<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stock-Report.Pdf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>  
                <h1 Class="text-center fs-4" style="text-align: center">Stock Report</h1>
                <h2 class="text-center fs-5" style="text-align: center">Total Product List</h2>
            <hr>
            <table class="table table-bordered table-striped table-hover custom_table">
                <thead class="table-dark">
                    <tr>
                        <th>#SL</th>
                        <th style="width: 25%">Product</th>
                        <th>Category</th>
                        <th>Stock In</th>
                        <th>Stock Out</th>
                        <th>Sale Return</th>
                        <th>Purchase Return</th>
                        <th>Damage</th>
                        <th>Available Stock</th>
                        <th>Available Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $data)
                        @php
                            $purchased_qty = purchased_qty($data);
                            $invoiced_qty = invoiced_qty($data);
                            $stock_qty = product_stock($data);
                            $returned_qty = returned_qty($data);
                            $damaged_qty = damaged_qty($data);
                            $return_pur_qty = return_pur_qty($data);
                            $factor = ($data->unit && $data->unit->related_value) ? $data->unit->related_value : 1;
                            $stock = product_fake_stock_val($data) * ($data->purchase_price / $factor);
                        @endphp
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $data->name }} - {{ $data->barcode }}</td>
                            <td>{{ $data->category->name }}</td>
                            <td>{{ $purchased_qty }}</td>
                            <td>{{ $invoiced_qty }}</td>
                            <td>{{ $returned_qty }}</td>
                            <td>{{ $return_pur_qty }}</td>
                            <td>{{ $damaged_qty }}</td>
                            <td>{{ $stock_qty }}</td>
                            <td>{{ $stock }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
