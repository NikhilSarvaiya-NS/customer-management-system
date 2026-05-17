$(document).ready(function () {

    // ─── AUTO-FETCH CUSTOMER DETAILS ON DROPDOWN CHANGE ───
    $('#customer_select').change(function () {
        var customer_id = $(this).val();
        if (customer_id === '') {
            clearCustomerFields();
            return;
        }
        $.ajax({
            url: 'get_customer.php',
            type: 'GET',
            data: { id: customer_id },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#f_code').val(data.customer_code);
                    $('#f_name').val(data.name);
                    $('#f_addr1').val(data.addr1);
                    $('#f_addr2').val(data.addr2);
                    $('#f_city').val(data.city);
                    $('#f_pincode').val(data.pincode);
                    $('#f_state').val(data.state);
                    $('#f_country').val(data.country);
                    $('#f_contact_person').val(data.contact_person);
                    $('#f_contact_number').val(data.contact_number);
                    $('#f_email').val(data.email);
                    $('#f_gstin').val(data.gstin);
                } else {
                    clearCustomerFields();
                    alert('Customer details not found!');
                }
            },
            error: function () {
                alert('Error fetching customer details. Please try again.');
            }
        });
    });

    // ─── CLEAR CUSTOMER FIELDS ───
    function clearCustomerFields() {
        $('#f_code, #f_name, #f_addr1, #f_addr2, #f_city, #f_pincode, #f_state, #f_country, #f_contact_person, #f_contact_number, #f_email, #f_gstin').val('');
    }

    // ─── ADD PRODUCT ROW ───
    $('#add_product_btn').click(function () {
        var rowCount = $('#product_rows .product-row').length + 1;
        var newRow = `
            <tr class="product-row">
                <td class="row-num">${rowCount}</td>
                <td><input type="text" name="product_name[]" class="form-control" placeholder="Product name" required></td>
                <td><input type="number" name="qty[]" class="form-control qty" min="1" value="1" required></td>
                <td><input type="number" name="price[]" class="form-control price" min="0" step="0.01" value="0.00" required></td>
                <td><input type="text" class="form-control row-total" value="0.00" disabled></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`;
        $('#product_rows').append(newRow);
        updateRowNumbers();
    });

    // ─── REMOVE PRODUCT ROW ───
    $(document).on('click', '.remove-row', function () {
        if ($('#product_rows .product-row').length > 1) {
            $(this).closest('tr').remove();
            updateRowNumbers();
            calculateAll();
        } else {
            alert('At least one product row is required!');
        }
    });

    // ─── UPDATE ROW NUMBERS ───
    function updateRowNumbers() {
        $('#product_rows .product-row').each(function (index) {
            $(this).find('.row-num').text(index + 1);
        });
    }

    // ─── AUTO CALCULATE ROW TOTAL ─── (Event Delegation for dynamic rows)
    $(document).on('input', '.qty, .price', function () {
        var row   = $(this).closest('tr');
        var qty   = parseFloat(row.find('.qty').val()) || 0;
        var price = parseFloat(row.find('.price').val()) || 0;
        var total = qty * price;
        row.find('.row-total').val(total.toFixed(2));
        calculateGrandTotal();
    });

    // ─── CALCULATE GRAND TOTAL ───
    function calculateGrandTotal() {
        var grandTotal = 0;
        $('.row-total').each(function () {
            grandTotal += parseFloat($(this).val()) || 0;
        });
        $('#grand_total_display').val('₹ ' + grandTotal.toFixed(2));
        $('#grand_total_hidden').val(grandTotal.toFixed(2));
    }

    // ─── CALCULATE ALL (used on page load) ───
    window.calculateAll = function() {
        $('#product_rows .product-row').each(function () {
            var qty   = parseFloat($(this).find('.qty').val()) || 0;
            var price = parseFloat($(this).find('.price').val()) || 0;
            $(this).find('.row-total').val((qty * price).toFixed(2));
        });
        calculateGrandTotal();
    };

    // ─── FORM VALIDATION BEFORE SUBMIT ───
    $('#quotationForm').submit(function (e) {
        var customer_id = $('#customer_select').val();
        if (!customer_id) {
            e.preventDefault();
            alert('Please select a customer!');
            return false;
        }
        var grandTotal = parseFloat($('#grand_total_hidden').val()) || 0;
        if (grandTotal <= 0) {
            e.preventDefault();
            alert('Please add at least one product with a valid price!');
            return false;
        }
    });

    // Initial calculation on page load
    calculateAll();
});
