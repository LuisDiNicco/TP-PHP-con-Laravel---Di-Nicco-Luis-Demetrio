<?php
/* 
// Descomentar para ejecutar los Test

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ReclutaApiTest extends TestCase
{
    #[Test]
    public function puede_obtener_lista_de_reclutas()
    {
        $response = $this->getJson('/api/reclutier');

        $response->assertStatus(200)
                 ->assertJsonStructure([]);
    }

    #[Test]
    public function puede_crear_un_recluta_valido()
    {
        $payload = [
            "name" => "luis demetrio",
            "suraname" => "di nicco",
            "birthday" => "2002/02/16",
            "documentType" => "DNI",
            "documentNumber" => 43664669
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'Luis Demetrio',
                     'suraname' => 'Di Nicco',
                     'documentType' => 'DNI'
                 ]);
    }

    // fecha muy antigua
    #[Test]
    public function rechaza_birthday_invalido()
    {
        $payload = [
            "name" => "luis",
            "suraname" => "di nicco",
            "birthday" => "1800/01/01", 
            "documentType" => "DNI",
            "documentNumber" => 12345678
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(400)
         ->assertJsonFragment([
             'message' => 'Error de validación',
             'birthday' => ['El birthday no puede ser anterior a 1900/01/01.']
         ]);
    }

    #[Test]
    public function rechaza_documentType_invalido()
    {
        $payload = [
            "name" => "luis",
            "suraname" => "di nicco",
            "birthday" => "2000/01/01",
            "documentType" => "PASSPORT",
            "documentNumber" => 12345678
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['documentType']);
    }

    // Fecha en formato incorrecto
    #[Test]
    public function rechaza_birthday_formato_incorrecto()
    {
        $payloads = [
            ["birthday" => "16/02/2002"],
            ["birthday" => "02-16-2002"],
            ["birthday" => "2002-02-16"],
            ["birthday" => "02/16/02"],
        ];

        foreach ($payloads as $payload) {
            $response = $this->postJson('/api/recluta', array_merge([
                "name" => "Luis",
                "suraname" => "Di Nicco",
                "documentType" => "CUIT",
                "documentNumber" => 20123456781
            ], $payload));

            $response->assertStatus(400)
                     ->assertJsonValidationErrors(['birthday']);
        }
    }

    // Nombre con letra ñ o acento
    #[Test]
    public function acepta_name_con_letras_especiales()
    {
        $payload = [
            "name" => "Ñora María",
            "suraname" => "Gómez",
            "birthday" => "2000/01/01",
            "documentType" => "DNI",
            "documentNumber" => 12345678
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201)
                ->assertJsonFragment([
                    'name' => 'Ñora María',
                    'suraname' => 'Gómez',
                ]);
    }

    // Nombre con símbolo extraño
    #[Test]
    public function rechaza_name_con_simbolos_no_permitidos()
    {
        $payload = [
            "name" => "?+-*Luis",
            "suraname" => "Di Nicco",
            "birthday" => "2000/01/01",
            "documentType" => "DNI",
            "documentNumber" => 12345678
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(400)
                ->assertJsonValidationErrors(['name']);
    }

    // documentType en minúscula debe normalizar a mayúscula y pasar
    #[Test]
    public function acepta_documentType_minuscula_y_normaliza()
    {
        $payload = [
            "name" => "Luis",
            "suraname" => "Di Nicco",
            "birthday" => "2000/01/01",
            "documentType" => "dni",
            "documentNumber" => 12345678
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['documentType' => 'DNI']);
    }

    // documentNumber fuera de rango de longitud
    #[Test]
    public function rechaza_documentNumber_fuera_de_rango()
    {
        $payload = [
            "name" => "Luis",
            "suraname" => "Di Nicco",
            "birthday" => "2000/01/01",
            "documentType" => "CUIT",
            "documentNumber" => 123 // demasiado corto
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['documentNumber']);
    }

    // birthday futuro
    #[Test]
    public function rechaza_birthday_futuro()
    {
        $futureDate = now()->addYear()->format('Y/m/d');

        $payload = [
            "name" => "Luis",
            "suraname" => "Di Nicco",
            "birthday" => $futureDate,
            "documentType" => "CUIT",
            "documentNumber" => 20123456781
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['birthday']);
    }

     // Nombre con espacios al inicio/final y dobles espacios
    #[Test]
    public function normaliza_name_con_espacios_extra()
    {
        $payload = [
            "name" => "  luis   demetrio ",
            "suraname" => " di  nicco  ",
            "birthday" => "2000/01/01",
            "documentType" => "DNI",
            "documentNumber" => 12345678
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'Luis Demetrio',
                     'suraname' => 'Di Nicco'
                 ]);
    }

    // Nombres y apellidos en mayúsculas completas → Title Case
    #[Test]
    public function normaliza_name_y_suraname_mayusculas()
    {
        $payload = [
            "name" => "LUIS DEMETRIO",
            "suraname" => "DI NICCO",
            "birthday" => "2000/01/01",
            "documentType" => "CUIT",
            "documentNumber" => 20123456781
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'Luis Demetrio',
                     'suraname' => 'Di Nicco'
                 ]);
    }

    // Edad calculada correctamente a partir de birthday
    #[Test]
    public function calcula_age_correctamente()
    {
        $expected_Age = 23;
        $birthday = now()->subYears($expected_Age)->format('Y/m/d');

        $payload = [
            "name" => "Luis",
            "suraname" => "Di Nicco",
            "birthday" => $birthday,
            "documentType" => "CUIT",
            "documentNumber" => 20123456781
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201)
                ->assertJsonFragment([
                    'age' => (int) $expected_Age
                ]);
    }

    // birthday límite superior e inferior
    #[Test]
    public function acepta_birthday_limites()
    {
        $payloads = [
            ["birthday" => "1900/01/01"],
            ["birthday" => now()->format('Y/m/d')]
        ];

        foreach ($payloads as $payload) {
            $response = $this->postJson('/api/recluta', array_merge([
                "name" => "Luis",
                "suraname" => "Di Nicco",
                "documentType" => "CUIT",
                "documentNumber" => 20123456781
            ], $payload));

            $response->assertStatus(201);
        }
    }

    // documentNumber longitud máxima
    #[Test]
    public function acepta_documentNumber_longitud_maxima()
    {
        $payload = [
            "name" => "Luis",
            "suraname" => "Di Nicco",
            "birthday" => "2000/01/01",
            "documentType" => "CUIT",
            "documentNumber" => 12345678901 // 11 dígitos
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201);
    }

    // Payload vacío → error
    #[Test]
    public function rechaza_payload_vacio()
    {
        $response = $this->postJson('/api/recluta', []);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['name', 'suraname', 'birthday', 'documentType', 'documentNumber']);
    }

    // Campo extra ignorado
    #[Test]
    public function ignora_campos_extra()
    {
        $payload = [
            "name" => "Luis",
            "suraname" => "Di Nicco",
            "birthday" => "2000/01/01",
            "documentType" => "CUIT",
            "documentNumber" => 20123456781,
            "extraField" => "valor no usado"
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201)
                 ->assertJsonMissing(['extraField']);
    }

    // Birthday 29/02 en año bisiesto → válido
    #[Test]
    public function acepta_birthday_29_febrero_en_bisiesto()
    {
        $payload = [
            "name" => "Luis",
            "suraname" => "Di Nicco",
            "birthday" => "2000/02/29", // 2000 es bisiesto
            "documentType" => "CUIT",
            "documentNumber" => 20123456781
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['birthday' => '2000/02/29/']);
    }

    // Birthday 29/02 en año no bisiesto → inválido
    #[Test]
    public function rechaza_birthday_29_febrero_fuera_de_bisiesto()
    {
        $payload = [
            "name" => "Luis",
            "suraname" => "Di Nicco",
            "birthday" => "2001/02/29", // 2001 no es bisiesto
            "documentType" => "CUIT",
            "documentNumber" => 20123456781
        ];

        $response = $this->postJson('/api/recluta', $payload);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['birthday']);
    }    
}
*/