# NETLEY

## Proyecto

NETLEY es un sistema de gestión jurídica.

## Stack

- Laravel 12
- PHP 8.2+
- FilamentPHP 4
- MySQL
- Composer

## Arquitectura funcional

El flujo principal del sistema es:

Visitante
→ Prospecto
→ Consulta Virtual
→ Ticket
→ Asignación
→ Cita
→ Cliente Ejecutivo
→ Caso
→ Historial

## Dominios principales

- Persona
- Consulta
- Ticket
- Asignación
- Cita
- Cliente Ejecutivo
- Caso
- Historial

## Reglas para Claude

Antes de modificar código:

1. Analizar primero la estructura existente.
2. Buscar modelos, recursos, migraciones y relaciones existentes.
3. No crear duplicados.
4. No modificar arquitectura sin explicarlo.
5. Mantener compatibilidad con Laravel 12.
6. Mantener compatibilidad con FilamentPHP 4.
7. Respetar las convenciones existentes del proyecto.
8. Antes de realizar cambios importantes, explicar qué archivos serán modificados.

## Documentación del proyecto

La documentación oficial del proyecto se encuentra en:

docs/

Esta carpeta está vinculada al Vault de Obsidian mediante un enlace
simbólico.

Estructura:

- docs/Arquitectura/
- docs/Backlog/
- docs/base de Datos/
- docs/Decisiones/
- docs/Dominios/
- docs/Graphify/
- docs/Procesos/

### Regla fundamental

Antes de realizar cambios importantes en el sistema:

1. Revisar la documentación relacionada en `docs/`.
2. Revisar el código existente.
3. Comparar documentación vs implementación actual.
4. Identificar posibles contradicciones.
5. Explicar las contradicciones antes de modificar arquitectura.
6. No inventar entidades, procesos o reglas de negocio.

### Fuente de verdad

Para el comportamiento REAL del sistema:

- El código fuente representa la implementación actual.
- La documentación de `docs/` representa la arquitectura,
  decisiones y conocimiento del proyecto.

Si existe una contradicción entre ambos, NO asumir cuál es correcta.

Informar la contradicción y solicitar una decisión cuando sea necesario.

### Actualización de documentación

Cuando una modificación cambie:

- arquitectura
- modelo de datos
- dominio
- flujo de negocio
- reglas de negocio
- decisiones arquitectónicas

se debe proponer actualizar la documentación correspondiente
en `docs/`.

No actualizar documentación automáticamente cuando el cambio sea
puramente interno y no afecte al comportamiento o arquitectura.