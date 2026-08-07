<?php
/**
 * Agente Legal Boliviano - Versión PHP (para hosting compartido tipo Banahosting)
 * ---------------------------------------------------------------------------
 * No requiere librerías externas, solo cURL (viene activado en casi todo
 * hosting cPanel/Banahosting por defecto).
 *
 * IMPORTANTE: guardá tu API key fuera del código si es posible, o al menos
 * asegurate de que este archivo NO sea públicamente accesible sin protección
 * (podés ponerlo en una carpeta que no esté expuesta directamente, o
 * protegerlo con .htaccess).
 */

// ============ CONFIGURACIÓN ============
// La API key se lee de la variable de entorno ANTHROPIC_API_KEY y, si no
// existe, del archivo .env de la raíz del proyecto (mismo que usa Laravel).
// Nunca la dejes escrita directamente en este archivo.
function obtenerAnthropicApiKey(): string
{
    $desdeEntorno = getenv('ANTHROPIC_API_KEY');
    if ($desdeEntorno !== false && $desdeEntorno !== '') {
        return $desdeEntorno;
    }

    $archivoEnv = __DIR__ . '/../.env';
    if (is_readable($archivoEnv)) {
        foreach (file($archivoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            $linea = trim($linea);
            if (str_starts_with($linea, 'ANTHROPIC_API_KEY=')) {
                $valor = substr($linea, strlen('ANTHROPIC_API_KEY='));
                return trim($valor, " \t\n\r\0\x0B\"'");
            }
        }
    }

    return '';
}

define('ANTHROPIC_API_KEY', obtenerAnthropicApiKey());
define('MODEL', 'claude-sonnet-4-5');

$SYSTEM_PROMPT = <<<EOT
Eres un agente de conversación especializado EXCLUSIVAMENTE en el ámbito legal
de Bolivia (procesos judiciales, trámites legales, códigos, leyes, derechos y
obligaciones dentro del sistema jurídico boliviano).

REGLAS ESTRICTAS:

1. ÁMBITO: Solo respondes preguntas relacionadas con leyes, procesos legales,
   trámites judiciales/administrativos, derechos y obligaciones dentro de
   Bolivia. Esto incluye derecho civil, penal, laboral, familiar, tributario,
   administrativo, constitucional, etc.

2. FUERA DE ÁMBITO: Si el usuario pregunta CUALQUIER cosa que no sea legal
   (recetas de cocina, deportes, tecnología, chistes, temas personales no
   legales, etc.), respondes ÚNICAMENTE con una variación breve y cordial de:
   "Lo siento, no puedo ayudarte con eso. Soy un agente de conversación
   especializado en el ámbito legal boliviano. ¿Tienes alguna consulta legal
   en la que pueda ayudarte?"
   No intentes responder la pregunta fuera de tema, ni parcialmente.

3. SALUDOS: Si el usuario solo saluda ("hola", "buen día"), respondes el
   saludo y te presentas brevemente como agente legal boliviano, invitando a
   la consulta legal.

4. PRECISIÓN Y HONESTIDAD: Cuando respondas sobre leyes o procesos, sé claro
   sobre qué tan seguro estás. Si no tienes certeza sobre un artículo o
   procedimiento específico, dilo explícitamente en vez de inventar datos.
   No inventes números de artículos, leyes o plazos.

5. DISCLAIMER OBLIGATORIO: Al final de cualquier respuesta legal sustantiva,
   agrega una breve nota aclarando que esto es información general y
   orientativa, y que para su caso concreto debe consultar con un abogado
   matriculado en Bolivia.

6. NO DES ASESORÍA DEFINITIVA: No le digas al usuario "haz esto y ganarás tu
   caso". En vez de eso, explica el proceso general, los pasos típicos, y qué
   tipo de profesional debería consultar.
EOT;

// ============ FUNCIÓN PRINCIPAL ============
function preguntarAlAgente($historial, $systemPrompt) {
    if (ANTHROPIC_API_KEY === '') {
        return ['error' => 'Falta configurar ANTHROPIC_API_KEY (variable de entorno o .env).'];
    }

    $url = 'https://api.anthropic.com/v1/messages';

    $data = [
        'model' => MODEL,
        'max_tokens' => 1000,
        'system' => $systemPrompt,
        'messages' => $historial,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        curl_close($ch);
        return ['error' => 'Error de conexión: ' . curl_error($ch)];
    }
    curl_close($ch);

    $decoded = json_decode($response, true);

    if ($httpCode !== 200) {
        $mensajeError = $decoded['error']['message'] ?? 'Error desconocido de la API';
        return ['error' => $mensajeError];
    }

    return ['texto' => $decoded['content'][0]['text']];
}

// ============ ENDPOINT (para usar vía AJAX/fetch desde tu software) ============
// Este bloque solo se ejecuta si el archivo recibe una petición POST con JSON.
// Así podés llamarlo desde tu frontend (JS, app, etc.) como una API propia.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);

    // Se espera: { "historial": [ {"role": "user", "content": "..."}, ... ] }
    $historial = $input['historial'] ?? [];

    if (empty($historial)) {
        echo json_encode(['error' => 'No se recibió historial de conversación']);
        exit;
    }

    $resultado = preguntarAlAgente($historial, $GLOBALS['SYSTEM_PROMPT']);
    echo json_encode($resultado);
    exit;
}

// ============ MODO CONSOLA / PRUEBA RÁPIDA (opcional, vía CLI: php agente_legal_bolivia.php) ============
if (php_sapi_name() === 'cli') {
    echo "Agente Legal Boliviano — escribe 'salir' para terminar\n\n";
    $historial = [];

    while (true) {
        echo "Tú: ";
        $pregunta = trim(fgets(STDIN));

        if (in_array(strtolower($pregunta), ['salir', 'exit', 'quit'])) {
            echo "¡Hasta luego!\n";
            break;
        }

        $historial[] = ['role' => 'user', 'content' => $pregunta];
        $resultado = preguntarAlAgente($historial, $SYSTEM_PROMPT);

        if (isset($resultado['error'])) {
            echo "\nError: " . $resultado['error'] . "\n\n";
            continue;
        }

        $historial[] = ['role' => 'assistant', 'content' => $resultado['texto']];
        echo "\nAgente: " . $resultado['texto'] . "\n\n";
    }
}