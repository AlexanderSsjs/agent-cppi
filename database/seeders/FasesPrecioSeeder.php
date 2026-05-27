<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FasesPrecioSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BLOQUE DE PRECIOS (Va a la tabla 'fases_precio')
        DB::table('fases_precio')->insert([
            [
                'nombre_fase' => 'Fase 1 (Preventa inicial)',
                'precio_unitario' => 40.00,
                'precio_promocional' => 37.00,
                'activa' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre_fase' => 'Fase 2 (Descuento regular)',
                'precio_unitario' => 50.00,
                'precio_promocional' => 40.00,
                'activa' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre_fase' => 'Fase 3 (Cierre de inscripciones)',
                'precio_unitario' => 60.00,
                'precio_promocional' => 50.00,
                'activa' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nombre_fase' => 'Fase 4 (Precio regular sin descuento)',
                'precio_unitario' => 70.00,
                'precio_promocional' => 60.00,
                'activa' => 0,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // 2. BLOQUE DE TEXTOS LARGOS (Va a la tabla 'configuraciones')
        DB::table('configuraciones')->insert([
            [
                'clave' => 'metodos_pago',
                'valor' => "MÉTODOS DE PAGO AUTORIZADOS:\n- Cuenta BBVA: 0011-0057-0239740503\n- Cuenta BCP: 49501522111064\n- Cuenta Interbank: 8983214510911\n- Pago por Yape o Plin al número: 959280078",
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'clave' => 'pasos_inscripcion',
                'valor' => "PASOS PARA LA INSCRIPCIÓN:\nPaso 1: Realice el pago en cualquiera de las cuentas indicadas.\nPaso 2: Escriba su nombre completo con lapicero sobre el voucher físico o impreso.\nPaso 3: Envíe una foto (no escaneado) del voucher el mismo día del pago al WhatsApp: +51 930449016 para validar la inscripción.\nCorreo de contacto adicional: tuportalacademico@gmail.com",
                'created_at' => now(), 'updated_at' => now(),
            ]
        ]);
    }
}