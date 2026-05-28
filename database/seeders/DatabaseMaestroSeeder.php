<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\MetodoPago;


class DatabaseMaestroSeeder extends Seeder
{
    public function run()
    {
        $cursos = [
            [
                'nombre_completo' => 'Gestión Integral del Ciclo de Inversión Pública',
                'abreviacion' => 'Invierte.pe',
                'modulos' => [
                    'Sistema Administrativo en el Sector Público',
                    'Marco Normativo del Sistema Nacional de Programación Multianual y Gestión de Inversiones',
                    'Identificación, Formulación y Evaluación',
                    'Fases del Ciclo de Inversión',
                    'Formulación de alternativas, evaluación ex post y operación',
                    'Módulo Práctico: Formulación de Proyectos',
                    'Evaluación Social (Excel) e Impacto Ambiental',
                    'Formatos: 6A, 7A, 8A, 8B, 9 y 12B',
                    'Ficha estándar para proyectos de saneamiento (urbano y rural)'
                ]
            ],
            [
                'nombre_completo' => 'Manejo de Sistemas Administrativos del Estado',
                'abreviacion' => 'Sistemas Administrativos',
                'modulos' => [
                    'Gestión por procesos: Política Nacional de Modernización, elementos y ciclo de mejora',
                    'Desarrollo, territorio, inversión y gestión pública',
                    'Sistema Invierte.pe: Marco normativo, fases, programación multianual y formulación',
                    'Registro de Cierre de Inversiones',
                    'Sistemas Administrativos: Integración, SIAF y SIGA (introducción)'
                ]
            ],
            [
                'nombre_completo' => 'Sistema Nacional de Información de Obras Públicas',
                'abreviacion' => 'INFOBRAS',
                'modulos' => [
                    'Introducción: Marco normativo, rol de la Contraloría y beneficios',
                    'Registro de datos de la obra: Ingreso, expediente técnico, ubicación y financiamiento',
                    'Supervisión y ejecución: Reportes de avance, evidencias y buenas prácticas',
                    'Cierre y liquidación: Actas, registro final, validación y errores frecuentes',
                    'Casos prácticos y simulación'
                ]
            ],
            [
                'nombre_completo' => 'Sistema Integrado de Administración Financiera del Estado',
                'abreviacion' => 'SIAF',
                'modulos' => [
                    'Introducción: Estructura, principios, alcances y rol de Unidades Ejecutoras',
                    'Componentes: Entorno, perfiles, estructura, clasificadores y fuentes de financiamiento',
                    'Presupuesto público: Fases, programación, registro de PIA, modificaciones y cadena programática',
                    'Certificación, compromiso y devengado: Registros, gestión de saldos, anulaciones y reportes'
                ]
            ],
            [
                'nombre_completo' => 'Sistema Integrado de Gestión Administrativa',
                'abreviacion' => 'SIGA',
                'modulos' => [
                    'Introducción: Importancia, rol y estructura general',
                    'Gestión de almacén y patrimonio: Kardex, transferencias, ajustes, altas, bajas y reportes',
                    'Gestión de requerimientos y abastecimiento: Registro, catálogo, consolidación, priorización y seguimiento'
                ]
            ],
            [
                'nombre_completo' => 'Contrataciones del Estado',
                'abreviacion' => 'GECE',
                'modulos' => [
                    'Marco general: Finalidad, principios, actores y bases',
                    'Procedimientos de selección: Tipos, fases, presentación de ofertas y buena pro',
                    'Actuaciones preparatorias: Requerimientos, mercado, valor estimado, PAC y certificación'
                ]
            ],
            [
                'nombre_completo' => 'Ofimática Profesional',
                'abreviacion' => 'Ofimatica',
                'modulos' => [
                    'Procesador de textos: Edición, formato, estilos, tablas, revisión y PDF',
                    'Presentaciones: Diseños, animaciones, transiciones y trabajo colaborativo',
                    'Hojas de cálculo y Power BI: Fórmulas, filtros, tablas dinámicas y paneles',
                    'Gestión digital: Nube, archivos, permisos y entorno Microsoft'
                ]
            ]
        ];

        foreach ($cursos as $data) {
            // Se eliminaron campos de precio
            Curso::create([
                'nombre_completo' => $data['nombre_completo'],
                'abreviacion' => $data['abreviacion'],
                'duracion_horas' => 80
            ])->modulos()->createMany(
                array_map(fn($m) => ['titulo' => $m], $data['modulos'])
            );
        }

        MetodoPago::insert([
            ['tipo' => 'banco', 'nombre' => 'BBVA', 'numero_cuenta' => '0011-0057-0239740503', 'titular' => 'Ingeniería Líder'],
            ['tipo' => 'banco', 'nombre' => 'BCP', 'numero_cuenta' => '49501522111064', 'titular' => 'Ingeniería Líder'],
            ['tipo' => 'banco', 'nombre' => 'Interbank', 'numero_cuenta' => '8983214510911', 'titular' => 'Ingeniería Líder'],
            ['tipo' => 'pago_movil', 'nombre' => 'Yape', 'telefono' => '959280078', 'titular' => 'Ingeniería Líder'],
            ['tipo' => 'pago_movil', 'nombre' => 'Plin', 'telefono' => '959280078', 'titular' => 'Ingeniería Líder'],
        ]);
    }
}