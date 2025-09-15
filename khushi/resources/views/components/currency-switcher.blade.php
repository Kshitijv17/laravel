<div class="currency-switcher d-inline-block me-2">
    <button onclick="switchCurrency('USD')" class="btn btn-outline-success btn-sm {{ session('currency', 'USD') === 'USD' ? 'active' : '' }}">
        💲 USD
    </button>
    <button onclick="switchCurrency('EUR')" class="btn btn-outline-success btn-sm {{ session('currency', 'USD') === 'EUR' ? 'active' : '' }}">
        💶 EUR
    </button>
    <button onclick="switchCurrency('INR')" class="btn btn-outline-success btn-sm {{ session('currency', 'USD') === 'INR' ? 'active' : '' }}">
        💰 INR
    </button>
</div>

<script>
function switchCurrency(currency) {
    fetch('/currency/' + currency, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error switching currency:', error);
    });
}
</script>

<style>
.currency-switcher .currency-symbol {
    margin-right: 8px;
    font-weight: bold;
}
.currency-switcher .dropdown-item.active {
    background-color: var(--bs-primary);
    color: white;
}
.currency-switcher .dropdown-item:hover {
    background-color: var(--bs-light);
}
</style>
