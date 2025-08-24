<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ReclutaController extends Controller
{
    // GET /api/reclutier  → devuelve JSON legible/normalizado
    public function index()
    {
        $url = rtrim(env('FIREBASE_URL'), '/') . '/reclutier.json';

        $resp = Http::get($url);
        if (!$resp->successful()) {
            return response()->json([
                'error' => 'No se pudo obtener datos de Firebase',
                'details' => $resp->body()
            ], $resp->status());
        }

        $data = $resp->json(); // puede ser null o un objeto {id: registro, ...}

        // Normalizamos y eliminamos duplicados
        $rows = collect($data ?? [])->map(function ($item) {
            $last = $item['suraname'] ?? $item['surname'] ?? $item['surename'] ?? '';
            $name = $item['name'] ?? '';
            $birthday = isset($item['birthday']) ? rtrim($item['birthday'], '/') : null;
            $age = $item['age'] ?? ($birthday ? $this->calcAgeFromYmd($birthday) : null);

            return [
                'name'           => $this->toTitle($name),
                'suraname'       => $this->toTitle($last),
                'birthday'       => $birthday,
                'age'            => $age,
                'documentType'   => isset($item['documentType']) ? strtoupper($item['documentType']) : null,
                'documentNumber' => $item['documentNumber'] ?? null,
            ];
        })->filter(function ($row) {
            // Elimina registros donde todo está vacío o null
            return collect($row)->filter(fn($v) => !is_null($v) && $v !== '')->isNotEmpty();
        })
        ->unique(function ($item) {
            return md5(json_encode($item));
        })
        ->values();

        return response()->json($rows);
    }

    // GET /reclutados → misma data pero en HTML (tabla simple)
    public function human()
    {
        $url = rtrim(env('FIREBASE_URL'), '/') . '/reclutier.json';
        $resp = Http::get($url);
        $data = $resp->json();

        $rows = collect($data ?? [])->map(function ($item) {
            $last = $item['suraname'] ?? $item['surname'] ?? $item['surename'] ?? '';
            $name = $item['name'] ?? '';
            $birthday = isset($item['birthday']) ? rtrim($item['birthday'], '/') : null;
            $age = $item['age'] ?? ($birthday ? $this->calcAgeFromYmd($birthday) : null);

            return [
                'name'           => $this->toTitle($name),
                'suraname'       => $this->toTitle($last),
                'birthday'       => $birthday,
                'age'            => $age,
                'documentType'   => isset($item['documentType']) ? strtoupper($item['documentType']) : null,
                'documentNumber' => $item['documentNumber'] ?? null,
            ];
        })->filter(function ($row) {
            // Elimina registros donde todo está vacío o null
            return collect($row)->filter(fn($v) => !is_null($v) && $v !== '')->isNotEmpty();
        })
        ->unique(function ($item) {
            return md5(json_encode($item));
        })
        ->values();

        return view('reclutados', ['rows' => $rows]);
    }

    // POST /api/recluta → valida, normaliza, arma payload y POSTea a Firebase
    public function store(Request $request)
    {
        //Forzar que Laravel trate este request como API
        //if (!$request->expectsJson()) {
           // $request->headers->set('Accept', 'application/json');
        //}

        // Rechazo peticiones que no contengan la cabezera accept
        if (!$request->expectsJson()) {
            return response()->json([
                'error' => 'Esta ruta requiere que la cabecera Accept sea application/json'
            ], 406); // 406 Not Acceptable
        }
        
        // Normalizamos de entrada (por si llega "dni" o "cuit")
        $request->merge([
            'documentType' => strtoupper($request->input('documentType', '')),
        ]);

        // Validaciones de negocio
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:1', 'max:100', 'regex:/^[\p{L}\s\'\-]+$/u'],
            'suraname' => ['required', 'string', 'min:1', 'max:100', 'regex:/^[\p{L}\s\'\-]+$/u'],
            'birthday' => ['required', 'date_format:Y/m/d', 'after_or_equal:1900/01/01', 'before_or_equal:today'],
            'documentType' => ['required', 'string', Rule::in(['CUIT', 'DNI'])],
            'documentNumber' => ['required', 'regex:/^\d{7,11}$/'],
        ], [
            'birthday.date_format' => 'El birthday debe tener formato YYYY/MM/DD.',
            'birthday.after_or_equal' => 'El birthday no puede ser anterior a 1900/01/01.',
            'birthday.before_or_equal' => 'El birthday no puede ser posterior a hoy.',
            'documentType.in' => 'documentType debe ser CUIT o DNI.',
            'documentNumber.regex' => 'documentNumber debe contener solo dígitos (7 a 11).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 400);
        }

        $validated = $validator->validated();

        // Normalización requerida
        $name = $this->toTitle($validated['name']);
        $last = $this->toTitle($validated['suraname']);
        $birthdayYmd = $validated['birthday']; // viene Y/m/d
        $age = $this->calcAgeFromYmd($birthdayYmd);

        // Armar payload final para Firebase (con la barra final en birthday)
        $payload = [
            'name'           => $name,
            'suraname'       => $last,
            'birthday'       => $birthdayYmd . '/',   // <- barra final como pide la consigna
            'age'            => $age,
            'documentType'   => $validated['documentType'],
            'documentNumber' => is_numeric($validated['documentNumber'])
                                ? (int) $validated['documentNumber']
                                : $validated['documentNumber'], // por si preferís string
        ];

        // POST a Firebase
        $url = rtrim(env('FIREBASE_URL'), '/') . '/reclutier.json';
        $firebase = Http::post($url, $payload);

        if (!$firebase->successful()) {
            return response()->json([
                'error' => 'No se pudo guardar en Firebase',
                'details' => $firebase->body(),
            ], $firebase->status());
        }

        // Firebase suele responder {"name": "-Clav3Generada"}
        return response()->json([
            'message'  => 'Recluta creado y enviado a Firebase',
            'saved'    => $payload,
            'firebase' => $firebase->json(),
        ], 201);
    }

    // Helpers
    private function toTitle(?string $value): ?string
    {
        if ($value === null) return null;
        $value = preg_replace('/\s+/', ' ', trim($value)); // elimina espacios extra
        return Str::of($value)->lower()->title(); // Title Case
    }

    private function calcAgeFromYmd(string $ymd): int
    {
        // Acepta "YYYY/MM/DD"
        $clean = rtrim($ymd, '/');
        $dt = Carbon::createFromFormat('Y/m/d', $clean);
        return $dt->diffInYears(now());;
    }
}
