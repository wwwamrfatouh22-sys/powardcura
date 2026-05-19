@props([
    'action' => url()->current(),
    'typeOptions' => [],
    'statusOptions' => [],
    'typeLabel' => 'Booking Type',
    'statusLabel' => 'Status',
    'searchLabel' => 'Search',
    'searchPlaceholder' => 'Search...',
    'showSearch' => false,
    'showType' => true,
    'showStatus' => true,
])

@php
    $dateOptions = [
        'all' => 'All dates',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 days',
        'last_30_days' => 'Last 30 days',
        'this_month' => 'This month',
    ];

    $selectedDateFilter = request('date_filter', 'all');
    $selectedType = request('type', 'all');
    $selectedStatus = request('status', 'all');
    $search = request('q');
    $fromDate = request('from_date');
    $toDate = request('to_date');
    $filterId = uniqid('table_filters_', false);
@endphp

@once
    <style>
        .table-filter-panel {
            margin: 0 auto 18px;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(248, 248, 248, 0.94);
            box-shadow: 0 10px 22px rgba(34, 52, 84, 0.10);
        }

        .table-filter-grid {
            display: grid;
            gap: 10px 12px;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            align-items: end;
        }

        .table-filter-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .table-filter-field label {
            font-size: 12px;
            font-weight: 700;
            color: #30445c;
        }

        .table-filter-field select,
        .table-filter-field input {
            width: 100%;
            min-height: 36px;
            padding: 7px 11px;
            border-radius: 10px;
            border: 1px solid #d8e3ef;
            background: #fbfdff;
            color: #314155;
            font-size: 13px;
            line-height: 1.2;
        }

        .table-filter-field select:focus,
        .table-filter-field input:focus {
            outline: none;
            border-color: rgba(15, 76, 150, 0.45);
            box-shadow: 0 0 0 4px rgba(15, 76, 150, 0.10);
            background: #fff;
        }

        .table-filter-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .table-filter-apply,
        .table-filter-reset {
            min-height: 36px;
            padding: 7px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .table-filter-apply {
            background: linear-gradient(135deg, #114a9f 0%, #1b64b9 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(15, 76, 150, 0.18);
        }

        .table-filter-reset {
            background: rgba(15, 76, 150, 0.10);
            color: #114a9f;
        }

        .table-filter-pagination {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
        }

        @media (max-width: 768px) {
            .table-filter-panel {
                padding: 12px;
                border-radius: 14px;
            }

            .table-filter-actions {
                width: 100%;
            }

            .table-filter-apply,
            .table-filter-reset {
                flex: 1 1 140px;
            }
        }
    </style>
@endonce

<form method="GET" action="{{ $action }}" class="table-filter-panel">
    <div class="table-filter-grid">
        <div class="table-filter-field">
            <label for="date_filter_{{ $filterId }}">Date Filter</label>
            <select id="date_filter_{{ $filterId }}" name="date_filter">
                @foreach($dateOptions as $value => $label)
                    <option value="{{ $value }}" @selected($selectedDateFilter === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="table-filter-field">
            <label for="from_date_{{ $filterId }}">From</label>
            <input id="from_date_{{ $filterId }}" type="date" name="from_date" value="{{ $fromDate }}">
        </div>

        <div class="table-filter-field">
            <label for="to_date_{{ $filterId }}">To</label>
            <input id="to_date_{{ $filterId }}" type="date" name="to_date" value="{{ $toDate }}">
        </div>

        @if($showSearch)
            <div class="table-filter-field">
                <label for="q_{{ $filterId }}">{{ $searchLabel }}</label>
                <input id="q_{{ $filterId }}" type="search" name="q" value="{{ $search }}" placeholder="{{ $searchPlaceholder }}">
            </div>
        @endif

        @if($showType)
            <div class="table-filter-field">
                <label for="type_{{ $filterId }}">{{ $typeLabel }}</label>
                <select id="type_{{ $filterId }}" name="type" @disabled(empty($typeOptions))>
                    <option value="all">All</option>
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if($showStatus)
            <div class="table-filter-field">
                <label for="status_{{ $filterId }}">{{ $statusLabel }}</label>
                <select id="status_{{ $filterId }}" name="status" @disabled(empty($statusOptions))>
                    <option value="all">All</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="table-filter-actions">
            <button type="submit" class="table-filter-apply">Apply</button>
            <a href="{{ $action }}" class="table-filter-reset">Reset Filters</a>
        </div>
    </div>
</form>
