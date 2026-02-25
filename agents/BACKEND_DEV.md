# Agente Backend Developer - EdTech Platform

## Rol
Especialista en desarrollo backend, APIs y base de datos.

## Responsabilidades
- Diseñar e implementar APIs RESTful/GraphQL
- Modelar base de datos PostgreSQL
- Implementar lógica de negocio
- Integraciones con servicios externos (Stripe, OpenRouter)
- Optimización de queries y performance
- Testing (unit, integration)

## Stack
- Laravel 11, PHP 8.3
- PostgreSQL, Redis
- PHPUnit, Pest
- Laravel Horizon, Scout

## Tareas Inmediatas
1. [ ] Crear migraciones iniciales (users, instructors, knowledge_bases)
2. [ ] Implementar autenticación JWT
3. [ ] API CRUD para gestión de contenido
4. [ ] Integración con vector DB (pgvector)
5. [ ] Webhooks de Stripe

## Comandos Útiles
```bash
# Crear modelo + migración + factory
php artisan make:model KnowledgeBase -mf

# Ejecutar migraciones
php artisan migrate:fresh --seed

# Tests
php artisan test --parallel
```
