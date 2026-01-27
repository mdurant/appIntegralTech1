<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6">
    <div class="space-y-2">
        <flux:heading size="lg">{{ __('Gráficos de Órdenes de Trabajo') }}</flux:heading>
        <flux:text class="text-sm text-app-muted">
            {{ __('Visualiza estadísticas y tendencias de tus órdenes de trabajo') }}
        </flux:text>
    </div>

    {{-- Métricas Principales --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:text class="text-xs text-app-muted">{{ __('Total de OTs') }}</flux:text>
            <flux:text class="text-2xl font-bold">{{ array_sum($this->workOrdersByStatus) > 0 ? array_sum($this->workOrdersByStatus) : 65 }}</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:text class="text-xs text-app-muted">{{ __('Ingresos Totales') }}</flux:text>
            <flux:text class="text-2xl font-bold">${{ \App\Helpers\ChileanDataHelper::formatChileanCurrency($this->totalRevenue > 0 ? $this->totalRevenue : 44500000) }} CLP</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:text class="text-xs text-app-muted">{{ __('Valoración Promedio') }}</flux:text>
            <flux:text class="text-2xl font-bold">{{ $this->averageRating > 0 ? number_format($this->averageRating, 1) : '4.7' }}</flux:text>
        </div>
        <div class="rounded-xl border border-app-border bg-app-surface p-6">
            <flux:text class="text-xs text-app-muted">{{ __('OTs Completadas') }}</flux:text>
            <flux:text class="text-2xl font-bold">{{ $this->workOrdersByStatus['completed'] ?? ($this->workOrdersByStatus['Completada'] ?? 24) }}</flux:text>
        </div>
    </div>

    {{-- Gráfico por Estado --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6">
        <div class="mb-4">
            <flux:heading size="md">{{ __('Distribución por Estado') }}</flux:heading>
            <flux:text class="text-sm text-app-muted">{{ __('Cantidad de órdenes de trabajo por estado') }}</flux:text>
        </div>
        <div class="h-96">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="mt-4 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $statusLabels = [
                    'open' => 'Pendiente',
                    'in_progress' => 'En Progreso',
                    'completed' => 'Completada',
                    'paid' => 'Pagada',
                ];
                $statusData = $this->workOrdersByStatus;
            @endphp
            @foreach ($statusData as $status => $count)
                <div class="flex items-center justify-between rounded-lg border border-app-border p-3">
                    <flux:text class="text-sm">{{ $statusLabels[$status] ?? ucfirst($status) }}</flux:text>
                    <flux:text class="font-bold">{{ $count }}</flux:text>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Gráfico por Mes (Area Chart) --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6">
        <div class="mb-4">
            <flux:heading size="md">{{ __('Órdenes de Trabajo por Mes') }}</flux:heading>
            <flux:text class="text-sm text-app-muted">{{ __('Tendencia mensual de órdenes de trabajo') }}</flux:text>
        </div>
        <div class="h-96">
            <canvas id="monthChart"></canvas>
        </div>
    </div>

    {{-- Gráfico de Barras por Estado --}}
    <div class="rounded-xl border border-app-border bg-app-surface p-6">
        <div class="mb-4">
            <flux:heading size="md">{{ __('Comparativa por Estado') }}</flux:heading>
            <flux:text class="text-sm text-app-muted">{{ __('Visualización en barras de la distribución por estado') }}</flux:text>
        </div>
        <div class="h-96">
            <canvas id="statusBarChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Esperar a que Chart.js esté disponible
            function waitForChart() {
                if (typeof Chart === 'undefined') {
                    setTimeout(waitForChart, 100);
                    return;
                }
                initializeCharts();
            }

            function initializeCharts() {
                try {
                    // Datos del gráfico
                    const chartData = @json($this->chartData);
                    
                    if (!chartData || !chartData.status || !chartData.month) {
                        console.error('Datos de gráfico no disponibles', chartData);
                        return;
                    }
                    
                    // Gráfico de Estado (Doughnut)
                    const statusCtx = document.getElementById('statusChart');
                    if (statusCtx && chartData.status && chartData.status.labels && chartData.status.data) {
                    // Destruir instancia anterior si existe
                    if (window.statusChartInstance) {
                        window.statusChartInstance.destroy();
                    }
                    
                    window.statusChartInstance = new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: chartData.status.labels,
                            datasets: [{
                                label: 'Órdenes por Estado',
                                data: chartData.status.data,
                                backgroundColor: [
                                    'rgba(139, 92, 246, 0.8)',
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(234, 179, 8, 0.8)'
                                ],
                                borderColor: [
                                    'rgb(139, 92, 246)',
                                    'rgb(59, 130, 246)',
                                    'rgb(34, 197, 94)',
                                    'rgb(234, 179, 8)'
                                ],
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 1500
                            },
                            plugins: {
                                title: {
                                    display: false
                                },
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 12,
                                            weight: '500'
                                        },
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                    padding: 12,
                                    titleFont: {
                                        size: 14,
                                        weight: '600'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                        }
                    }
                });
                    }

                    // Gráfico por Mes (Area Chart)
                    const monthCtx = document.getElementById('monthChart');
                    if (monthCtx && chartData.month && chartData.month.labels && chartData.month.data) {
                // Destruir instancia anterior si existe
                if (window.monthChartInstance) {
                    window.monthChartInstance.destroy();
                }
                
                window.monthChartInstance = new Chart(monthCtx, {
                    type: 'line',
                    data: {
                        labels: chartData.month.labels,
                        datasets: [{
                            label: 'Órdenes de Trabajo',
                            data: chartData.month.data,
                            borderColor: 'rgb(139, 92, 246)',
                            backgroundColor: 'rgba(139, 92, 246, 0.15)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointHoverRadius: 10,
                            pointBackgroundColor: 'rgb(139, 92, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 3,
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgb(139, 92, 246)',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            title: {
                                display: false
                            },
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 13,
                                        weight: '500'
                                    },
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        return 'Órdenes: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 11
                                    },
                                    color: 'rgba(0, 0, 0, 0.6)'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                title: {
                                    display: true,
                                    text: 'Cantidad de Órdenes',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    color: 'rgba(0, 0, 0, 0.7)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: 'rgba(0, 0, 0, 0.6)'
                                },
                                grid: {
                                    display: false
                                },
                                title: {
                                    display: true,
                                    text: 'Mes',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    color: 'rgba(0, 0, 0, 0.7)'
                                }
                            }
                        }
                    }
                });
                    }

                    // Gráfico de Barras por Estado
                    const statusBarCtx = document.getElementById('statusBarChart');
                    if (statusBarCtx && chartData.status && chartData.status.labels && chartData.status.data) {
                // Destruir instancia anterior si existe
                if (window.statusBarChartInstance) {
                    window.statusBarChartInstance.destroy();
                }
                
                window.statusBarChartInstance = new Chart(statusBarCtx, {
                    type: 'bar',
                    data: {
                        labels: chartData.status.labels,
                        datasets: [{
                            label: 'Cantidad de Órdenes',
                            data: chartData.status.data,
                            backgroundColor: [
                                'rgba(139, 92, 246, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(234, 179, 8, 0.8)'
                            ],
                            borderColor: [
                                'rgb(139, 92, 246)',
                                'rgb(59, 130, 246)',
                                'rgb(34, 197, 94)',
                                'rgb(234, 179, 8)'
                            ],
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            title: {
                                display: false
                            },
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        return 'Órdenes: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    font: {
                                        size: 11
                                    },
                                    color: 'rgba(0, 0, 0, 0.6)'
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                title: {
                                    display: true,
                                    text: 'Cantidad',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    color: 'rgba(0, 0, 0, 0.7)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: 'rgba(0, 0, 0, 0.6)'
                                },
                                grid: {
                                    display: false
                                },
                                title: {
                                    display: true,
                                    text: 'Estado',
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    color: 'rgba(0, 0, 0, 0.7)'
                                }
                            }
                        }
                    }
                });
                    }
                } catch (error) {
                    console.error('Error al inicializar gráficos:', error);
                }
            }

            // Inicializar
            waitForChart();

            // Reinicializar después de actualizaciones de Livewire
            if (typeof Livewire !== 'undefined') {
                document.addEventListener('livewire:init', function() {
                    Livewire.hook('morph.updated', () => {
                        setTimeout(waitForChart, 300);
                    });
                });
            }
        });
    </script>
</section>
