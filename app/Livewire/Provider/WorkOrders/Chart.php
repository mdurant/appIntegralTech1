<?php

namespace App\Livewire\Provider\WorkOrders;

use App\Models\WorkOrder;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Chart extends Component
{
    #[Computed]
    public function workOrdersByStatus(): array
    {
        $data = WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Si no hay datos, usar datos de ejemplo
        if (empty($data)) {
            $jsonPath = public_path('data/work-orders-chart.json');
            if (file_exists($jsonPath)) {
                $jsonData = json_decode(file_get_contents($jsonPath), true);
                if ($jsonData && isset($jsonData['status'])) {
                    // Convertir labels a keys y data a valores
                    $statusMap = [
                        'Pendiente' => 'open',
                        'En Progreso' => 'in_progress',
                        'Completada' => 'completed',
                        'Pagada' => 'paid',
                    ];
                    $result = [];
                    foreach ($jsonData['status']['labels'] as $index => $label) {
                        $key = $statusMap[$label] ?? strtolower($label);
                        $result[$key] = $jsonData['status']['data'][$index] ?? 0;
                    }

                    return $result;
                }
            }

            return ['open' => 8, 'in_progress' => 15, 'completed' => 24, 'paid' => 18];
        }

        return $data;
    }

    #[Computed]
    public function workOrdersByMonth(): array
    {
        // SQLite compatible query
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite') {
            $data = WorkOrder::query()
                ->where('awarded_to_user_id', auth()->id())
                ->selectRaw("strftime('%Y-%m', created_at) as month, COUNT(*) as count")
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();
        } else {
            // MySQL/MariaDB
            $data = WorkOrder::query()
                ->where('awarded_to_user_id', auth()->id())
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();
        }

        // Si no hay datos, usar datos de ejemplo
        if (empty($data)) {
            $jsonPath = public_path('data/work-orders-chart.json');
            if (file_exists($jsonPath)) {
                $jsonData = json_decode(file_get_contents($jsonPath), true);
                if ($jsonData && isset($jsonData['month'])) {
                    // Convertir labels a formato Y-m
                    $result = [];
                    // Mapeo de meses en español a inglés
                    $monthMap = [
                        'Ene' => 'Jan',
                        'Feb' => 'Feb',
                        'Mar' => 'Mar',
                        'Abr' => 'Apr',
                        'May' => 'May',
                        'Jun' => 'Jun',
                        'Jul' => 'Jul',
                        'Ago' => 'Aug',
                        'Sep' => 'Sep',
                        'Oct' => 'Oct',
                        'Nov' => 'Nov',
                        'Dic' => 'Dec',
                    ];

                    foreach ($jsonData['month']['labels'] as $index => $label) {
                        // Convertir "Jul 2025" o "Ago 2025" a "2025-07" o "2025-08"
                        $parts = explode(' ', $label);
                        if (count($parts) === 2) {
                            $monthEsp = $parts[0];
                            $year = $parts[1];
                            $monthEng = $monthMap[$monthEsp] ?? $monthEsp;
                            $date = \Carbon\Carbon::createFromFormat('M Y', "{$monthEng} {$year}");
                            $result[$date->format('Y-m')] = $jsonData['month']['data'][$index] ?? 0;
                        }
                    }

                    return $result;
                }
            }

            // Fallback
            return [
                '2025-07' => 5,
                '2025-08' => 8,
                '2025-09' => 12,
                '2025-10' => 15,
                '2025-11' => 18,
                '2025-12' => 22,
                '2026-01' => 25,
            ];
        }

        return $data;
    }

    #[Computed]
    public function totalRevenue(): float
    {
        $revenue = (float) WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->whereNotNull('final_price')
            ->sum('final_price');

        // Si no hay datos, usar datos de ejemplo
        if ($revenue == 0) {
            $jsonPath = public_path('data/work-orders-chart.json');
            if (file_exists($jsonPath)) {
                $jsonData = json_decode(file_get_contents($jsonPath), true);
                if ($jsonData && isset($jsonData['totalRevenue'])) {
                    return (float) $jsonData['totalRevenue'];
                }
            }

            return 44500000.0; // Fallback
        }

        return $revenue;
    }

    #[Computed]
    public function averageRating(): float
    {
        $ratings = WorkOrder::query()
            ->where('awarded_to_user_id', auth()->id())
            ->with('ratings')
            ->get()
            ->flatMap->ratings;

        if ($ratings->isEmpty()) {
            // Si no hay datos, usar datos de ejemplo
            $jsonPath = public_path('data/work-orders-chart.json');
            if (file_exists($jsonPath)) {
                $jsonData = json_decode(file_get_contents($jsonPath), true);
                if ($jsonData && isset($jsonData['averageRating'])) {
                    return (float) $jsonData['averageRating'];
                }
            }

            return 4.7; // Fallback
        }

        return round($ratings->avg('rating'), 2);
    }

    #[Computed]
    public function chartData(): array
    {
        $statusData = $this->workOrdersByStatus;
        $monthData = $this->workOrdersByMonth;

        // Si no hay datos, usar datos de ejemplo del JSON
        if (empty($statusData) && empty($monthData)) {
            return $this->getSampleChartData();
        }

        // Mapear estados a español
        $statusLabels = [
            'open' => 'Pendiente',
            'in_progress' => 'En Progreso',
            'completed' => 'Completada',
            'paid' => 'Pagada',
        ];

        $mappedStatusLabels = [];
        $mappedStatusData = [];
        foreach ($statusData as $status => $count) {
            $mappedStatusLabels[] = $statusLabels[$status] ?? ucfirst($status);
            $mappedStatusData[] = $count;
        }

        // Si no hay datos de mes, usar datos de ejemplo
        if (empty($monthData)) {
            $sampleData = $this->getSampleChartData();

            return [
                'status' => [
                    'labels' => $mappedStatusLabels,
                    'data' => $mappedStatusData,
                ],
                'month' => $sampleData['month'],
            ];
        }

        return [
            'status' => [
                'labels' => $mappedStatusLabels,
                'data' => $mappedStatusData,
            ],
            'month' => [
                'labels' => array_map(function ($month) {
                    $date = \Carbon\Carbon::createFromFormat('Y-m', $month);
                    // Mapeo de meses en inglés a español para mostrar
                    $monthMap = [
                        'Jan' => 'Ene',
                        'Feb' => 'Feb',
                        'Mar' => 'Mar',
                        'Apr' => 'Abr',
                        'May' => 'May',
                        'Jun' => 'Jun',
                        'Jul' => 'Jul',
                        'Aug' => 'Ago',
                        'Sep' => 'Sep',
                        'Oct' => 'Oct',
                        'Nov' => 'Nov',
                        'Dec' => 'Dic',
                    ];
                    $monthEng = $date->format('M');
                    $monthEsp = $monthMap[$monthEng] ?? $monthEng;

                    return "{$monthEsp} {$date->format('Y')}";
                }, array_keys($monthData)),
                'data' => array_values($monthData),
            ],
        ];
    }

    protected function getSampleChartData(): array
    {
        // Cargar datos de ejemplo desde JSON
        $jsonPath = public_path('data/work-orders-chart.json');

        if (file_exists($jsonPath)) {
            $jsonData = json_decode(file_get_contents($jsonPath), true);

            if ($jsonData) {
                return [
                    'status' => [
                        'labels' => $jsonData['status']['labels'] ?? ['Pendiente', 'En Progreso', 'Completada', 'Pagada'],
                        'data' => $jsonData['status']['data'] ?? [8, 15, 24, 18],
                    ],
                    'month' => [
                        'labels' => $this->convertMonthLabelsToSpanish($jsonData['month']['labels'] ?? ['Jul 2025', 'Aug 2025', 'Sep 2025', 'Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026']),
                        'data' => $jsonData['month']['data'] ?? [5, 8, 12, 15, 18, 22, 25],
                    ],
                ];
            }
        }

        // Fallback si no existe el JSON
        return [
            'status' => [
                'labels' => ['Pendiente', 'En Progreso', 'Completada', 'Pagada'],
                'data' => [8, 15, 24, 18],
            ],
            'month' => [
                'labels' => $this->convertMonthLabelsToSpanish(['Jul 2025', 'Aug 2025', 'Sep 2025', 'Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026']),
                'data' => [5, 8, 12, 15, 18, 22, 25],
            ],
        ];
    }

    protected function convertMonthLabelsToSpanish(array $labels): array
    {
        $monthMap = [
            'Jan' => 'Ene',
            'Feb' => 'Feb',
            'Mar' => 'Mar',
            'Apr' => 'Abr',
            'May' => 'May',
            'Jun' => 'Jun',
            'Jul' => 'Jul',
            'Aug' => 'Ago',
            'Sep' => 'Sep',
            'Oct' => 'Oct',
            'Nov' => 'Nov',
            'Dec' => 'Dic',
        ];

        return array_map(function ($label) use ($monthMap) {
            $parts = explode(' ', $label);
            if (count($parts) === 2) {
                $monthEng = $parts[0];
                $year = $parts[1];
                $monthEsp = $monthMap[$monthEng] ?? $monthEng;

                return "{$monthEsp} {$year}";
            }

            return $label;
        }, $labels);
    }

    public function mount(): void
    {
        abort_unless(! auth()->user()->isGuest() && ! auth()->user()->isClient(), 403);
    }

    public function render()
    {
        return view('livewire.provider.work-orders.chart')
            ->layout('layouts.app', ['title' => __('Gráficos de Órdenes de Trabajo')]);
    }
}
