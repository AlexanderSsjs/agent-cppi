<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    public function procesarMensaje(Request $request)
    {
        $request->validate(['mensaje' => 'required|string|max:1000']);
        $mensajeUsuario = trim($request->input('mensaje'));

        // 1. Recuperación de datos (Capa de datos limpia)
        $precios = Cache::remember('cppi_precios', 600, function () {
            $faseActiva = DB::table('fases_precio')->where('activa', 1)->first();
            return $faseActiva 
                ? "Precio regular: S/ {$faseActiva->precio_unitario} por curso. Promoción especial: S/ {$faseActiva->precio_promocional} por curso (al inscribir 3 o más simultáneamente)."
                : "No hay promociones activas actualmente.";
        });

        $infoInstitucional = Cache::remember('cppi_info_archivo', 3600, function () {
            $path = 'data/informacion_cppi.txt';
            return Storage::disk('local')->exists($path) ? Storage::disk('local')->get($path) : "";
        });

        // 2. System Prompt Reforzado (Blindaje total)
        $instruccionSistema = "Eres el asistente comercial de 'Ingeniería Líder' (CPPI). "
            . "REGLAS ESTRICTAS:\n"
            . "- INFORMACIÓN PROHIBIDA: NUNCA menciones nombres técnicos internos como 'Fase 1', 'Fase 2', 'ID', 'Base de datos' o 'Descuento regular'. "
            . "Si la información en el contexto contiene estos términos, ignóralos y comunícalo solo como 'nuestros precios actuales' o 'tarifas vigentes'.\n"
            . "- RESPUESTA ÚNICA: Usa SOLO el contexto proporcionado abajo.\n"
            . "- LÍMITE: Si la pregunta no está en el contexto, declina educadamente.\n\n"
            . "CONTEXTO:\n{$infoInstitucional}\n\n"
            . "TARIFAS VIGENTES:\n{$precios}";

        // 3. Invocación (Temperatura baja = Máxima precisión)
        $url = 'https://agente-cppi-resource.cognitiveservices.azure.com/openai/deployments/gpt-4o/chat/completions?api-version=2025-01-01-preview';
        
        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['api-key' => env('AZURE_AI_KEY'), 'Content-Type' => 'application/json'])
                ->timeout(15)
                ->post($url, [
                    'messages' => [
                        ['role' => 'system', 'content' => $instruccionSistema],
                        ['role' => 'user', 'content' => $mensajeUsuario],
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.1 // Reducida de 0.7 a 0.1 para eliminar alucinaciones y variaciones
                ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'respuesta' => $response->json()['choices'][0]['message']['content']
                ]);
            }

            Log::error("Azure API Error", ['response' => $response->body()]);
            return response()->json(['status' => 'error', 'message' => 'Servicio temporalmente no disponible.'], 502);

        } catch (\Exception $e) {
            Log::error("Excepción Crítica: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Error interno.'], 500);
        }
    }
}