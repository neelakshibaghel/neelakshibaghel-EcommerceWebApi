<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
            margin: 40px;
            color: #333;
        }

        h2 {
            color: #2c3e50;
            text-align: center;
        }

        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 500px;
            margin: 0 auto 30px;
        }

        form label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-top: 4px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 10px;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: white;
        }

        button[type="button"] {
            background-color: #e74c3c;
            color: white;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: center;
        }

        th {
            background-color: #2980b9;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td button {
            background-color: #2ecc71;
            color: white;
            margin: 0 5px;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
        }

        td button:last-child {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>

<h2>Product Management</h2>

<!-- Add / Edit Form -->
<form id="productForm">
    <input type="hidden" id="productId">
    <label>Client Name: <input type="text" id="ClientName" required></label>
    <label>Product Name: <input type="text" id="ProductName" required></label>
    <label>Product Price: <input type="text" id="ProductPrice"></label>
    <label>Store: <input type="text" id="Store"></label>
    <label>Status: 
        <select id="Status" required>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>
    </label>
    <button type="submit">Save</button>
    <button type="button" onclick="clearForm()">Clear</button>
</form>

<!-- Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Client Name</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Store</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="productTableBody">
    </tbody>
</table>

<script>
    const apiBase = 'http://127.0.0.1:8000/api'; 

    // Load all products
    function loadProducts() {
        $.post(`${apiBase}/products-list`, function (response) {
            const rows = response.DataList.map(row =>
                `<tr>
                    <td>${row.Id}</td>
                    <td>${row.ClientName}</td>
                    <td>${row.ProductName}</td>
                    <td>${row.ProductPrice}</td>
                    <td>${row.Store}</td>
                    <td>${row.Status}</td>
                    <td>
                        <button onclick='editProduct(${row.Id}, ${JSON.stringify(row.ClientName)}, ${JSON.stringify(row.ProductName)}, ${JSON.stringify(row.ProductPrice)}, ${JSON.stringify(row.Store)}, ${JSON.stringify(row.Status)})'>Edit</button>
                        <button onclick="deleteProduct(${row.Id})">Delete</button>
                    </td>
                </tr>`
            );
            $('#productTableBody').html(rows.join(''));
        });
    }

    // Save or Update product
    $('#productForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#productId').val();

        const data = {
            id: id || undefined,
            ClientName: $('#ClientName').val(),
            ProductName: $('#ProductName').val(),
            ProductPrice: $('#ProductPrice').val(),
            Store: $('#Store').val(),
            Status: $('#Status').val(),
            AddedBy: 1,
            UpdatedBy: 1
        };

        const url = id ? `${apiBase}/update-products` : `${apiBase}/store-products`;

        $.post(url, data, function (res) {
            alert(res.Message || 'Saved successfully');
            $('#productForm')[0].reset();
            $('#productId').val('');
            loadProducts();
        }).fail(err => {
            if (err.responseJSON && err.responseJSON.errors) {
                let messages = '';
                const errors = err.responseJSON.errors;
                for (let key in errors) {
                    messages += errors[key][0] + '\n';
                }
                alert(messages);
            } else if (err.responseJSON && err.responseJSON.message) {
                alert(err.responseJSON.message);
            } else {
                alert("Something went wrong.");
            }
            console.error(err.responseJSON);
        });
    });

    // Edit product
    function editProduct(id, clientName, productName, productPrice, store, status) {
        $('#productId').val(id);
        $('#ClientName').val(clientName);
        $('#ProductName').val(productName);
        $('#ProductPrice').val(productPrice);
        $('#Store').val(store);
        $('#Status').val(status);
    }

    // Delete product
    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
            $.post(`${apiBase}/delete-products`, { id }, function (res) {
                alert(res.result || 'Deleted successfully');
                loadProducts();
            });
        }
    }

    // Clear form
    function clearForm() {
        $('#productForm')[0].reset();
        $('#productId').val('');
        loadProducts();
    }

    // Initial load
    $(document).ready(function () {
        loadProducts();
    });
</script>

</body>
</html>
