/**
 * File: sales-list.init.js
 * Description: Refined JavaScript for the Sales List page with improved functionality
 */

var salesTable;
var currentSaleId = null;
var currentSaleItems = [];
var isDataLoading = false;

$(document).ready(function () {
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Date Range Picker with better defaults
    var today = new Date();
    
    $('#datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        endDate: today,
        clearBtn: true
    });
    
    // Set default date to today
    $('#start-date').datepicker('setDate', today);
    $('#end-date').datepicker('setDate', today);

    // Setup toastr options
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    };

    // Set today button as active by default
    $('#todayFilter').addClass('active');

    // Initialize Sales DataTable
    loadSalesTable();

    // Search function with delay and better error handling
    let searchTimeout;
    $('#searchTableList').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            showLoading();
            salesTable.search($('#searchTableList').val()).draw();
        }, 500);
    });

    // Date filter quick buttons
    $('#todayFilter').click(function() {
        if (isDataLoading) return;
        const today = new Date();
        
        $('#start-date').datepicker('setDate', today);
        $('#end-date').datepicker('setDate', today);
        $(this).addClass('active').siblings().removeClass('active');
        showLoading();
        loadSalesTable();
    });

    $('#yesterdayFilter').click(function() {
        if (isDataLoading) return;
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        $('#start-date').datepicker('setDate', yesterday);
        $('#end-date').datepicker('setDate', yesterday);
        $(this).addClass('active').siblings().removeClass('active');
        showLoading();
        loadSalesTable();
    });

    $('#thisWeekFilter').click(function() {
        if (isDataLoading) return;
        const today = new Date();
        const dayOfWeek = today.getDay();
        // Get first day of week (Sunday = 0, so we calculate from Sunday)
        const firstDay = new Date(today);
        firstDay.setDate(today.getDate() - dayOfWeek);
        $('#start-date').datepicker('setDate', firstDay);
        $('#end-date').datepicker('setDate', today);
        $(this).addClass('active').siblings().removeClass('active');
        showLoading();
        loadSalesTable();
    });

    $('#thisMonthFilter').click(function() {
        if (isDataLoading) return;
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        $('#start-date').datepicker('setDate', firstDay);
        $('#end-date').datepicker('setDate', today);
        $(this).addClass('active').siblings().removeClass('active');
        showLoading();
        loadSalesTable();
    });

    // Apply Filters button click
    $('#applyFilters').click(function() {
        if (isDataLoading) return;
        $('.btn-group .btn').removeClass('active');
        showLoading();
        loadSalesTable();
    });

    // Reset Filters button click
    $('#resetFilters').click(function() {
        if (isDataLoading) return;
        $('#start-date').datepicker('setDate', today);
        $('#end-date').datepicker('setDate', today);
        $('#searchTableList').val('');
        $('.btn-group .btn').removeClass('active');
        $('#todayFilter').addClass('active');
        showLoading();
        loadSalesTable();
    });

    // Export to Excel button click
    $('#exportExcel').click(function() {
        salesTable.button('.buttons-excel').trigger();
    });

    // Void Sale button click
    $('.void-sale-btn').click(function() {
        if (!currentSaleId) return;
        
        $('#void-confirmation-message').text('Are you sure you want to void this entire sale?');
        $('#confirm-void-btn').data('void-type', 'sale');
        $('#confirm-void-btn').data('void-id', currentSaleId);
        $('#confirmVoidModal').modal('show');
    });

    // Confirm Void button click
    $('#confirm-void-btn').click(function() {
        const voidType = $(this).data('void-type');
        const voidId = $(this).data('void-id');
        
        if (voidType === 'sale') {
            voidSale(voidId);
        } else if (voidType === 'item') {
            voidSaleItem(voidId);
        }
        
        $('#confirmVoidModal').modal('hide');
    });
});

/**
 * Show loading overlay
 */
function showLoading() {
    isDataLoading = true;
    $('#loading-overlay').removeClass('d-none');
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    isDataLoading = false;
    $('#loading-overlay').addClass('d-none');
}

/**
 * Load Sales data into the DataTable
 */
function loadSalesTable() {
    var startDate = $('#start-date').val();
    var endDate = $('#end-date').val();
    var searchTerm = $('#searchTableList').val();
    
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#sales-table')) {
        salesTable.destroy();
    }


    const params = new URLSearchParams(window.location.search);
    const isVoidPage = params.get('type'); 
    if (isVoidPage == 'void') { show_only_voided = 1 ; }
    else { show_only_voided = 0 ; }
    
    // Initialize DataTable with AJAX source
    salesTable = $('#sales-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'sales-list',
            type: 'GET',
            data: function(d) {
                d.start_date = startDate;
                d.end_date = endDate;
                d.search = searchTerm;
                // Default to showing only non-voided sales
                d.show_voided = 0;
                d.show_only_voided = show_only_voided;
                return d;
            },
            dataSrc: function(json) {
                hideLoading();
                return json.data || [];
            },
            error: function(xhr, error, thrown) {
                hideLoading();
                console.error("DataTable error:", error, thrown);
                toastr.error('Failed to load sales data. Please try again.');
            }
        },
        columns: [
            { 
                data: 'order_id',
                render: function(data) {
                    return '<span class="fw-bold">#' + data + '</span>';
                }
            },
            { 
                data: 'sale_items',
                render: function(data, type, row) {
                    let total = 0;
                    if (data && Array.isArray(data) && data.length > 0) {
                        data.forEach(function(item) {
                            if (!item.is_void) {
                                total += parseFloat(item.total_amount || 0);
                            }
                        });
                    }
                    return formatCurrency(total);
                }
            },
            { 
                data: 'user',
                render: function(data) {
                    return data && data.name ? data.name : '-';
                }
            },
            {
                data: 'created_at',
                render: function(data) {
                    return formatDate(data);
                }
            },
            { 
                data: 'is_void',
                render: function(data) {
                    return formatStatus(data);
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data) {
                    return '<button type="button" class="btn btn-primary btn-sm view-details-btn">View Details</button>';
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data) {
                    if (data.is_void) {
                        return '<span class="badge bg-light text-dark">Voided</span>';
                    } else {
                        return '<button type="button" class="btn btn-danger btn-sm void-btn">Void</button>';
                    }
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            paginate: {
                next: '<i class="mdi mdi-chevron-right"></i>',
                previous: '<i class="mdi mdi-chevron-left"></i>',
                first: '<i class="mdi mdi-chevron-double-left"></i>',
                last: '<i class="mdi mdi-chevron-double-right"></i>'
            },
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            search: "",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            emptyTable: "No sales found",
            zeroRecords: "No matching records found",
            infoFiltered: "(filtered from _MAX_ total records)"
        },
        drawCallback: function() {
            setupViewDetailsButtons();
            setupVoidButtons();
            hideLoading();
        },
        pagingType: "full_numbers",
        pageLength: 15,
        lengthChange: true,
        lengthMenu: [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>><"clear">B',
        buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                title: 'Sales Report',
                filename: 'sales_report_' + formatDateForExport(new Date()),
                className: 'hidden-button buttons-excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            }
        ],
        // Add these options for better pagination with large datasets
        deferRender: true,
        scroller: true,
        scrollY: 500,
        scrollCollapse: true
    });

    // Handle DataTable pagination errors
    salesTable.on('error.dt', function(e, settings, techNote, message) {
        console.error('DataTable error:', message);
        hideLoading();
        toastr.error('An error occurred while loading data. Please try again.');
    });
}

/**
 * Format date for export filename
 */
function formatDateForExport(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return year + month + day;
}

/**
 * Format date to a more readable format
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        console.error("Date formatting error:", e);
        return dateString;
    }
}

/**
 * Format payment method with icons
 */
function formatPaymentMethod(method) {
    if (!method) return '-';
    
    switch (method.toLowerCase()) {
        case 'card':
            return '<div><i class="bx bx-credit-card me-1"></i>Card</div>';
        case 'cash':
            return '<div><i class="bx bx-money me-1"></i>Cash</div>';
        case 'fonepay':
            return '<div><i class="bx bx-mobile-alt me-1"></i>FonePay</div>';
        case 'online':
            return '<div><i class="bx bx-globe me-1"></i>Online</div>';
        default:
            return '<div><i class="bx bx-money me-1"></i>' + method + '</div>';
    }
}

/**
 * Format status with badge
 */
function formatStatus(isVoid) {
    if (isVoid) {
        return '<span class="badge badge-soft-danger">Voided</span>';
    } else {
        return '<span class="badge badge-soft-success">Active</span>';
    }
}

/**
 * Setup view details buttons
 */
function setupViewDetailsButtons() {
    $('.view-details-btn').off('click').on('click', function() {
        const rowData = salesTable.row($(this).closest('tr')).data();
        if (rowData && rowData.order_id) {
            loadSaleDetails(rowData.order_id);
        } else {
            toastr.error('Could not retrieve sale details');
        }
    });
}

/**
 * Setup void buttons
 */
function setupVoidButtons() {
    $('.void-btn').off('click').on('click', function() {
        const rowData = salesTable.row($(this).closest('tr')).data();
        if (rowData && rowData.order_id) {
            $('#void-confirmation-message').text('Are you sure you want to void this sale?');
            $('#confirm-void-btn').data('void-type', 'sale');
            $('#confirm-void-btn').data('void-id', rowData.order_id);
            $('#confirmVoidModal').modal('show');
        } else {
            toastr.error('Could not retrieve sale information');
        }
    });
}

/**
 * Load sale details from API
 */
function loadSaleDetails(orderId) {
    showLoading();
    $.ajax({
        url: 'sales/' + orderId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success && response.data) {
                displaySaleDetails(response.data);
            } else {
                toastr.error(response.message || 'Failed to load sale details');
            }
        },
        error: function(xhr) {
            hideLoading();
            console.error("Sale details error:", xhr);
            toastr.error('Failed to load sale details. Please try again.');
        }
    });
}

/**
 * Display sale details in modal
 */
function displaySaleDetails(sale) {
    if (!sale) {
        toastr.error('Sale details not found');
        return;
    }
    
    currentSaleId = sale.order_id;
    currentSaleItems = sale.sale_items || [];
    
    // Update sale overview information
    $('.sale-order-id').text('#' + sale.order_id);
    $('.sale-date').text(formatDate(sale.created_at));
    $('.sale-user').text(sale.user ? sale.user.name : '-');
    $('.sale-payment-method').html(formatPaymentMethod(sale.payment_method));
    $('.sale-status').html(formatStatus(sale.is_void));
    
    // Update void sale button state
    if (sale.is_void) {
        $('.void-sale-btn').prop('disabled', true);
        $('.void-sale-btn').text('Sale Voided');
    } else {
        $('.void-sale-btn').prop('disabled', false);
        $('.void-sale-btn').text('Void Entire Sale');
    }
    
    // Clear and populate sale items table
    const itemsTableBody = $('#sale-items-table tbody');
    itemsTableBody.empty();
    
    let totalAmount = 0;
    let totalQuantity = 0;
    let allItemsVoided = true;
    
    if (currentSaleItems.length > 0) {
        currentSaleItems.forEach(function(item) {
            // Track non-voided items to determine if all items are voided
            if (!item.is_void) {
                allItemsVoided = false;
                totalAmount += parseFloat(item.total_amount || 0);
                totalQuantity += parseInt(item.qty || 0);
            }
            
            const row = `
                <tr class="${item.is_void ? 'table-danger' : ''}">
                    <td>${item.token_id ? '#' + item.token_id : '-'}</td>
                    <td>${item.item && item.item.name ? item.item.name : 'Unknown'}</td>
                    <td>${item.category && item.category.name ? item.category.name : '-'}</td>
                    <td>${formatCurrency(item.selling_price || 0)}</td>
                    <td>${item.qty || 0}</td>
                    <td>${formatCurrency(item.total_amount || 0)}</td>
                    <td>${item.is_void ? '<span class="badge badge-soft-danger">Voided</span>' : '<span class="badge badge-soft-success">Active</span>'}</td>
                    <td>
                        ${!item.is_void && !sale.is_void && item.token_id ? 
                            `<button type="button" class="btn btn-sm btn-danger void-item-btn" data-token="${item.token_id}">Void Item</button>` : 
                            ''}
                    </td>
                </tr>
            `;
            itemsTableBody.append(row);
        });
        
        // If all items are voided but sale is not, update the UI to reflect this
        if (allItemsVoided && !sale.is_void && currentSaleItems.length > 0) {
            $('.sale-status').html(formatStatus(true));
            $('.void-sale-btn').prop('disabled', true);
            $('.void-sale-btn').text('All Items Voided');
        }
    } else {
        itemsTableBody.append('<tr><td colspan="8" class="text-center">No items found for this sale</td></tr>');
    }
    
    // Update total amount and items
    $('.sale-total-amount').text(formatCurrency(totalAmount));
    $('.sale-total-qty').text(totalQuantity);
    $('.sale-total-items').text(currentSaleItems.length);
    
    // Setup void item buttons
    setupVoidItemButtons();
    
    // Show modal
    $('#saleDetailsModal').modal('show');
}

/**
 * Setup void item buttons
 */
function setupVoidItemButtons() {
    $('.void-item-btn').off('click').on('click', function() {
        const tokenId = $(this).data('token');
        if (tokenId) {
            $('#void-confirmation-message').text('Are you sure you want to void this item?');
            $('#confirm-void-btn').data('void-type', 'item');
            $('#confirm-void-btn').data('void-id', tokenId);
            $('#confirmVoidModal').modal('show');
        } else {
            toastr.error('Could not identify the item to void');
        }
    });
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    if (isNaN(amount)) amount = 0;
    return 'Rs. ' + parseFloat(amount).toFixed(2);
}

/**
 * Void a sale
 */
function voidSale(orderId) {
    if (!orderId) {
        toastr.error('Invalid sale ID');
        return;
    }
    
    showLoading();
    $.ajax({
        url: 'sales/' + orderId + '/void',
        type: 'PUT',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                toastr.success('Sale voided successfully');
                // Reload DataTable
                salesTable.ajax.reload();
                // If sale details modal is open, reload sale details
                if ($('#saleDetailsModal').hasClass('show') && currentSaleId === orderId) {
                    loadSaleDetails(orderId);
                }
            } else {
                toastr.error(response.message || 'Failed to void sale');
            }
        },
        error: function(xhr) {
            hideLoading();
            console.error("Void sale error:", xhr);
            toastr.error(xhr.responseJSON?.message || 'Failed to void sale. Please try again.');
        }
    });
}

/**
 * Void a sale item
 */
function voidSaleItem(tokenId) {
    if (!tokenId) {
        toastr.error('Invalid item ID');
        return;
    }
    
    showLoading();
    $.ajax({
        url: 'sale-items/' + tokenId + '/void',
        type: 'PUT',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.success) {
                toastr.success('Item voided successfully');
                
                // If all items are now voided and the whole sale has been voided
                if (response.sale_voided === true) {
                    toastr.info('All items voided. Sale marked as void.');
                }
                
                // If sale details modal is open, reload sale details
                if ($('#saleDetailsModal').hasClass('show') && currentSaleId) {
                    loadSaleDetails(currentSaleId);
                }
                
                // Reload DataTable to update totals
                salesTable.ajax.reload(null, false);
            } else {
                toastr.error(response.message || 'Failed to void item');
            }
        },
        error: function(xhr) {
            hideLoading();
            console.error("Void item error:", xhr);
            toastr.error(xhr.responseJSON?.message || 'Failed to void item. Please try again.');
        }
    });
}