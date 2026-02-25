# Rol: DBA (Database Administrator)

## Responsabilidades Principales
- Diseño y modelado de base de datos
- Optimización de queries y performance
- Backup y recovery
- Seguridad de datos
- Migraciones y schema changes
- Monitoreo de salud de BD

## Stack
- PostgreSQL 16
- Redis (cache/sessions)
- pgAdmin / TablePlus
- Laravel Migrations

## Modelado de Datos

### Convenciones de Nombres
- Tablas: plural, snake_case (`knowledge_bases`)
- Columnas: snake_case (`created_at`)
- Foreign keys: `{table}_id` (`instructor_id`)
- Índices: `{tabla}_{columna}_index`

### Tipos de Datos Preferidos
| Concepto | Tipo PostgreSQL |
|----------|-----------------|
| IDs primarios | `UUID` o `BIGSERIAL` |
| Texto corto | `VARCHAR(255)` |
| Texto largo | `TEXT` |
| JSON flexible | `JSONB` |
| Fechas | `TIMESTAMP WITH TIME ZONE` |
| Dinero | `DECIMAL(10,2)` |
| Arrays | `TEXT[]`, `INTEGER[]` |
| Vectores IA | `VECTOR(1536)` (pgvector) |

## Optimización

### Índices Críticos
```sql
-- Búsquedas frecuentes
CREATE INDEX idx_users_email ON users(email);

-- Búsqueda de texto
CREATE INDEX idx_kb_search ON knowledge_bases 
USING GIN(to_tsvector('spanish', title || ' ' || description));

-- Vectores (similitud coseno)
CREATE INDEX idx_chunks_embedding ON content_chunks 
USING ivfflat (embedding_vector vector_cosine_ops);
```

### Queries Lentas
- Identificar con `EXPLAIN ANALYZE`
- Agregar índices donde falten
- Denormalizar si es necesario (caching)
- Particionar tablas grandes

## Backup Strategy
```bash
# Backup diario automatizado
pg_dump -Fc edtech_prod > backup_$(date +%Y%m%d).dump

# Retención: 7 días locales, 30 días en S3
```

## Migraciones Laravel
```php
// Crear migración
php artisan make:migration create_knowledge_bases_table

// Ejecutar
php artisan migrate

// Rollback
php artisan migrate:rollback

// Fresh (cuidado en prod!)
php artisan migrate:fresh --seed
```

## Health Checks
- Conexiones activas
- Disk space
- Query performance
- Replication lag (si aplica)
