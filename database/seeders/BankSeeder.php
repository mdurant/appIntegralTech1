<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $response = Http::timeout(10)->get('https://portalpersonas.bancochile.cl/tef/properties/bancos-activos.json');

            if ($response->successful()) {
                $banks = $response->json();

                if (is_array($banks)) {
                    foreach ($banks as $bankData) {
                        Bank::updateOrCreate(
                            ['codigo' => $bankData['codigo']],
                            [
                                'codigo' => $bankData['codigo'],
                                'nombre' => $bankData['nombre'],
                                'cuenta' => $bankData['cuenta'] ?? null,
                                'tipo' => $bankData['tipo'] ?? null,
                                'producto' => $bankData['producto'] ?? null,
                                'activo' => $bankData['activo'] ?? true,
                            ]
                        );
                    }
                }
            } else {
                $this->command->warn('No se pudo obtener los bancos desde la API. Usando datos de respaldo.');
                $this->seedFallbackBanks();
            }
        } catch (\Exception $e) {
            $this->command->warn('Error al obtener bancos desde la API: '.$e->getMessage());
            $this->command->warn('Usando datos de respaldo.');
            $this->seedFallbackBanks();
        }
    }

    private function seedFallbackBanks(): void
    {
        $fallbackBanks = [
            [
                'codigo' => '001',
                'nombre' => 'BANCO DE CHILE - EDWARDS',
                'cuenta' => null,
                'tipo' => 'BANCO',
                'producto' => null,
                'activo' => true,
            ],
            [
                'codigo' => '012',
                'nombre' => 'BANCO DEL ESTADO DE CHILE',
                'cuenta' => null,
                'tipo' => 'BANCO',
                'producto' => null,
                'activo' => true,
            ],
            [
                'codigo' => '016',
                'nombre' => 'BCI (BCO DE CREDITO E INV)',
                'cuenta' => null,
                'tipo' => 'BANCO',
                'producto' => null,
                'activo' => true,
            ],
            [
                'codigo' => '037',
                'nombre' => 'BANCO SANTANDER-CHILE',
                'cuenta' => null,
                'tipo' => 'BANCO',
                'producto' => null,
                'activo' => true,
            ],
        ];

        foreach ($fallbackBanks as $bankData) {
            Bank::updateOrCreate(
                ['codigo' => $bankData['codigo']],
                $bankData
            );
        }
    }
}
