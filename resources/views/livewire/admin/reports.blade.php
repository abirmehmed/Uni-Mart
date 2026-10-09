<div>
    <div class="mb-6">
        <p class="mb-1 font-mono text-xs uppercase tracking-widest text-amber-dark">Reports</p>
        <h1 class="font-display text-3xl font-bold uppercase tracking-tight text-ink">Sales calendar</h1>
        <p class="font-mono text-xs text-ink/40">Revenue, profit, and top sellers by day</p>
    </div>

    <div wire:key="kpi-{{ $year }}-{{ $month }}" class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="border border-ink/10 bg-white p-4 shadow-lg shadow-ink/5">
            <p class="mb-1 font-mono text-[10px] uppercase tracking-widest text-ink/40">Revenue this month</p>
            <p class="font-display text-3xl font-bold text-ink"
                x-data="{ val: 0 }"
                x-init="let target = {{ $this->monthSummary['revenueCents'] / 100 }}; let start = null; function step(ts){ if(!start) start = ts; let p = Math.min((ts - start) / 700, 1); val = (target * p).toFixed(2); if (p < 1) requestAnimationFrame(step); } requestAnimationFrame(step)"
                x-text="'$' + val"
            ></p>
        </div>
        <div class="border border-ledger/30 bg-ledger/5 p-4 shadow-lg shadow-ink/5">
            <p class="mb-1 font-mono text-[10px] uppercase tracking-widest text-ledger">Profit this month</p>
            <p class="font-display text-3xl font-bold text-ledger"
                x-data="{ val: 0 }"
                x-init="let target = {{ $this->monthSummary['profitCents'] / 100 }}; let start = null; function step(ts){ if(!start) start = ts; let p = Math.min((ts - start) / 700, 1); val = (target * p).toFixed(2); if (p < 1) requestAnimationFrame(step); } requestAnimationFrame(step)"
                x-text="'$' + val"
            ></p>
        </div>
        <div class="border border-ink/10 bg-white p-4 shadow-lg shadow-ink/5">
            <p class="mb-1 font-mono text-[10px] uppercase tracking-widest text-ink/40">Orders this month</p>
            <p class="font-display text-3xl font-bold text-ink"
                x-data="{ val: 0 }"
                x-init="let target = {{ $this->monthSummary['orderCount'] }}; let start = null; function step(ts){ if(!start) start = ts; let p = Math.min((ts - start) / 700, 1); val = Math.floor(target * p); if (p < 1) requestAnimationFrame(step); } requestAnimationFrame(step)"
                x-text="val"
            ></p>
        </div>
        <div class="border border-amber-dark/30 bg-amber/5 p-4 shadow-lg shadow-ink/5">
            <p class="mb-1 font-mono text-[10px] uppercase tracking-widest text-amber-dark">Best day</p>
            @if ($this->monthSummary['bestDayDate'])
                <p class="font-display text-xl font-bold text-ink">${{ number_format($this->monthSummary['bestDayCents'] / 100, 2) }}</p>
                <p class="font-mono text-[11px] text-ink/50">{{ \Carbon\Carbon::parse($this->monthSummary['bestDayDate'])->format('M j') }}</p>
            @else
                <p class="font-display text-xl font-bold text-ink/20">&mdash;</p>
            @endif
        </div>
    </div>

    <div
        wire:ignore
        wire:key="chart-{{ $year }}-{{ $month }}"
        x-data
        x-init="$nextTick(() => {
            Chart.getChart($refs.trendCanvas)?.destroy();
            new Chart($refs.trendCanvas, {
                type: 'line',
                data: {
                    labels: @js(collect($this->monthlyTrend)->pluck('day')),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: @js(collect($this->monthlyTrend)->pluck('revenue')),
                            borderColor: '#E8A33D',
                            backgroundColor: '#E8A33D22',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2,
                        },
                        {
                            label: 'Profit',
                            data: @js(collect($this->monthlyTrend)->pluck('profit')),
                            borderColor: '#4C7A5E',
                            backgroundColor: '#4C7A5E22',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { font: { family: 'IBM Plex Mono', size: 11 }, color: '#14181A' } },
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Day of month', font: { family: 'IBM Plex Mono', size: 10 }, color: '#14181A99' },
                            ticks: { font: { family: 'IBM Plex Mono', size: 10 } },
                            grid: { display: false },
                        },
                        y: {
                            title: { display: true, text: 'Revenue / Profit (USD)', font: { family: 'IBM Plex Mono', size: 10 }, color: '#14181A99' },
                            ticks: { font: { family: 'IBM Plex Mono', size: 10 }, callback: (v) => '$' + v },
                        },
                    },
                },
            });
        })"
        class="mb-6 border border-ink/10 bg-white p-5 shadow-lg shadow-ink/5"
    >
        <p class="mb-3 font-mono text-[11px] uppercase tracking-widest text-ink/40">Revenue &amp; profit this month</p>
        <div style="height: 220px;">
            <canvas x-ref="trendCanvas"></canvas>
        </div>
    </div>

    <div
        wire:ignore
        wire:key="products-chart-{{ $year }}-{{ $month }}"
        x-data
        x-init="$nextTick(() => {
            Chart.getChart($refs.productsCanvas)?.destroy();
            new Chart($refs.productsCanvas, {
                type: 'bar',
                data: {
                    labels: @js(array_keys($this->productsSoldThisMonth)),
                    datasets: [{
                        label: 'Units sold',
                        data: @js(array_values($this->productsSoldThisMonth)),
                        backgroundColor: '#C97F1F',
                        borderRadius: 2,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            title: { display: true, text: 'Units sold', font: { family: 'IBM Plex Mono', size: 10 }, color: '#14181A99' },
                            ticks: { font: { family: 'IBM Plex Mono', size: 10 } },
                        },
                        y: { ticks: { font: { family: 'IBM Plex Mono', size: 10 } }, grid: { display: false } },
                    },
                },
            });
        })"
        class="mb-6 border border-ink/10 bg-white p-5 shadow-lg shadow-ink/5"
    >
        <p class="mb-3 font-mono text-[11px] uppercase tracking-widest text-ink/40">Top products this month (units sold)</p>
        <div style="height: 260px;">
            <canvas x-ref="productsCanvas"></canvas>
        </div>
    </div>

    @php
        $hm = $this->yearHeatmap;
        $shades = ['bg-ink/[0.06]', 'bg-amber/25', 'bg-amber/50', 'bg-amber/80', 'bg-amber-dark'];
    @endphp
    <div class="mb-6 border border-ink/10 bg-white p-5 shadow-lg shadow-ink/5">
        <div class="mb-4 flex flex-wrap items-baseline justify-between gap-2">
            <p class="font-mono text-[11px] uppercase tracking-widest text-ink/40">Revenue &middot; last 365 days</p>
            <p class="font-mono text-[11px] text-ink/50">${{ number_format($hm['totalCents'] / 100, 2) }} &middot; {{ $hm['activeDays'] }} active days</p>
        </div>
        <div class="overflow-x-auto pb-2">
            <div class="inline-block">
                <div class="mb-1 grid" style="grid-template-columns: repeat({{ count($hm['weeks']) }}, 12px); gap: 3px;">
                    @foreach ($hm['weeks'] as $i => $week)
                        <div class="whitespace-nowrap font-mono text-[10px] text-ink/40">{{ $hm['monthLabels'][$i] ?? '' }}</div>
                    @endforeach
                </div>
                <div class="grid" style="grid-auto-flow: column; grid-template-rows: repeat(7, 12px); grid-auto-columns: 12px; gap: 3px;">
                    @foreach ($hm['weeks'] as $week)
                        @foreach ($week as $cell)
                            @if ($cell === null)
                                <div></div>
                            @else
                                <div class="rounded-sm {{ $shades[$cell['level']] }}" title="{{ \Carbon\Carbon::parse($cell['date'])->format('M j, Y') }}: ${{ number_format($cell['cents'] / 100, 2) }}"></div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mt-2 flex items-center justify-end gap-1 font-mono text-[10px] text-ink/40">
            <span class="mr-1">Less</span>
            @foreach ($shades as $s)
                <span class="h-3 w-3 rounded-sm {{ $s }}"></span>
            @endforeach
            <span class="ml-1">More</span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
        <div class="lg:col-span-3 border border-ink/10 bg-white shadow-lg shadow-ink/5">
            <div class="flex items-center justify-between border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
                <button wire:click="previousMonth" class="rounded-sm px-2 py-1 font-mono text-xs text-white/60 transition-colors hover:text-white">&larr;</button>
                <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">
                    {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                </span>
                <button wire:click="nextMonth" class="rounded-sm px-2 py-1 font-mono text-xs text-white/60 transition-colors hover:text-white">&rarr;</button>
            </div>

            <div class="p-4">
                <div class="mb-2 grid grid-cols-7 gap-1">
                    @foreach (['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $d)
                        <div class="py-1 text-center font-mono text-[10px] uppercase tracking-wide text-ink/30">{{ $d }}</div>
                    @endforeach
                </div>

                @foreach ($this->calendarWeeks as $week)
                    <div class="mb-1 grid grid-cols-7 gap-1">
                        @foreach ($week as $date)
                            @if ($date === null)
                                <div></div>
                            @else
                                @php
                                    $hasRevenue = $this->dailyTotals->has($date);
                                    $isSelected = $date === $selectedDate;
                                    $isToday = $date === now()->toDateString();
                                @endphp
                                <button
                                    wire:click="selectDate('{{ $date }}')"
                                    class="relative flex aspect-square flex-col items-center justify-center rounded-sm font-mono text-xs transition-colors
                                        {{ $isSelected ? 'bg-ink text-white shadow-sm' : 'text-ink/70 hover:bg-steel' }}
                                        {{ $isToday && ! $isSelected ? 'ring-1 ring-amber-dark' : '' }}"
                                >
                                    {{ \Carbon\Carbon::parse($date)->day }}
                                    @if ($hasRevenue)
                                        <span class="absolute bottom-1 h-1 w-1 rounded-full {{ $isSelected ? 'bg-amber' : 'bg-ledger' }}"></span>
                                    @endif
                                </button>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2 border border-ink/10 bg-white shadow-lg shadow-ink/5">
            <div class="border-b-2 border-dashed border-ink/15 bg-ink px-5 py-3">
                <span class="font-mono text-[11px] uppercase tracking-widest text-white/60">
                    {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('l, M j') : 'Select a date' }}
                </span>
            </div>

            @if (! $this->selectedDaySummary['hasOrders'])
                <p class="p-8 text-center font-mono text-sm text-ink/30">No orders on this day.</p>
            @else
                <div class="space-y-5 p-5">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="border border-ink/10 p-3">
                            <p class="mb-1 font-mono text-[10px] uppercase tracking-widest text-ink/40">Revenue</p>
                            <p class="font-display text-xl font-bold text-ink">${{ number_format($this->selectedDaySummary['revenueCents'] / 100, 2) }}</p>
                        </div>
                        <div class="border border-ledger/30 bg-ledger/5 p-3">
                            <p class="mb-1 font-mono text-[10px] uppercase tracking-widest text-ledger">Profit</p>
                            <p class="font-display text-xl font-bold text-ledger">${{ number_format($this->selectedDaySummary['profitCents'] / 100, 2) }}</p>
                        </div>
                    </div>

                    <div class="font-mono text-xs text-ink/60">
                        <div class="flex justify-between border-b border-dotted border-ink/15 py-1.5">
                            <span class="uppercase tracking-wide text-ink/40">Orders</span>
                            <span class="text-ink">{{ $this->selectedDaySummary['orderCount'] }}</span>
                        </div>
                        <div class="flex justify-between border-b border-dotted border-ink/15 py-1.5">
                            <span class="uppercase tracking-wide text-ink/40">Online</span>
                            <span class="text-ink">${{ number_format($this->selectedDaySummary['onlineCents'] / 100, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-dotted border-ink/15 py-1.5">
                            <span class="uppercase tracking-wide text-ink/40">POS</span>
                            <span class="text-ink">${{ number_format($this->selectedDaySummary['posCents'] / 100, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-dotted border-ink/15 py-1.5">
                            <span class="uppercase tracking-wide text-ink/40">Cost of goods</span>
                            <span class="text-ink">${{ number_format($this->selectedDaySummary['costCents'] / 100, 2) }}</span>
                        </div>
                    </div>

                    @if ($this->selectedDaySummary['topProductName'])
                        <div class="border border-amber-dark/30 bg-amber/5 p-3">
                            <p class="mb-1 font-mono text-[10px] uppercase tracking-widest text-amber-dark">Top seller</p>
                            <p class="text-sm font-semibold text-ink">{{ $this->selectedDaySummary['topProductName'] }}</p>
                            <p class="font-mono text-xs text-ink/50">{{ $this->selectedDaySummary['topProductQty'] }} sold</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
