<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Availability Check</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .availability-card {
            width: 255px;
            background: #ffffff;
            border: 1px solid #ddd;
            padding: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
            font-family: Arial, sans-serif;
        }

        .availability-card h4 {
            margin: 0 0 12px 0;
            font-size: 14px;
            font-weight: 700;
            color: #2f1c5c;
        }

        .date-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .date-input {
            width: 190px;
            height: 32px;
            border: 1px solid #c5c8d1;
            border-radius: 5px;
            padding: 0 12px;
            font-size: 12px;
            color: #6c7280;
            outline: none;
            background-color: #fff;
        }

        .calendar-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            color: #8a8a8a;
            font-size: 20px;
            cursor: pointer;
        }

        .bottom-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stock-box {
            width: 144px;
            height: 24px;
            border: 1px solid #c5c8d1;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .check-btn {
            width: 70px;
            height: 26px;
            border: none;
            border-radius: 2px;
            background-color: #351f63;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .check-btn:hover {
            background-color: #2a184f;
        }
    </style>
</head>
<body>

<div class="availability-card">
    <h4>Availability Check</h4>

    <form action="{{ route('availability.check') }}" method="GET">
        <input type="hidden" name="product_type" value="{{ $productType ?? 'HT' }}">

        <div class="date-row">
            <input 
                type="text" 
                id="dateRange" 
                name="date_range"
                class="date-input"
                placeholder="20 Okt 2025 - 23 Okt 2025"
                readonly
                value="{{ isset($pickupDate, $returnDate) ? $pickupDate . ' - ' . $returnDate : '' }}"
            >

            <button type="button" class="calendar-btn" id="openCalendar">
                📅
            </button>
        </div>

        <input type="hidden" name="pickup_date" id="pickupDate" value="{{ $pickupDate ?? '' }}">
        <input type="hidden" name="return_date" id="returnDate" value="{{ $returnDate ?? '' }}">
<select name="product_type" class="product-select" required>
    <option value="">Pilih Unit</option>

    @foreach ($productTypes as $type)
        <option value="{{ $type }}" {{ ($productType ?? '') == $type ? 'selected' : '' }}>
            {{ $type }}
        </option>
    @endforeach
</select>
        <div class="bottom-row">
            <div class="stock-box">
                {{ $availableStock ?? 0 }} Units
            </div>

            <button type="submit" class="check-btn">
                Check
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const dateRangePicker = flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        altInput: false,
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                const pickupDate = instance.formatDate(selectedDates[0], "Y-m-d");
                const returnDate = instance.formatDate(selectedDates[1], "Y-m-d");

                document.getElementById('pickupDate').value = pickupDate;
                document.getElementById('returnDate').value = returnDate;
            }
        }
    });

    document.getElementById('openCalendar').addEventListener('click', function () {
        dateRangePicker.open();
    });
</script>

</body>
</html>