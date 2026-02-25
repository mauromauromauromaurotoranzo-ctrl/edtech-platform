# Arquitectura Técnica - Plataforma Educativa IA

## Visión General

Sistema de aprendizaje adaptativo donde instructores crean repositorios de conocimiento estructurado, y estudiantes interactúan mediante IA generativa para una experiencia de aprendizaje personalizada.

## Decisión Arquitectónica: Monolito Modular

**Opción elegida:** Monolito modular con separación clara de dominios

**Justificación:**
- Equipo pequeño (Mauro + posibles colaboradores)
- Necesidad de velocidad de desarrollo (MVP)
- Stack familiar (Laravel/React)
- Escalabilidad vertical inicial, horizontal posterior

## Stack Tecnológico

### Backend
- **Framework:** Laravel 11 (PHP 8.3)
- **Base de datos:** PostgreSQL 16
- **Cache:** Redis
- **Queue:** Laravel Horizon + Redis
- **Búsqueda:** Meilisearch
- **Storage:** AWS S3 / DigitalOcean Spaces

### Frontend
- **Web:** Next.js 14 (App Router) + TypeScript
- **Estilos:** Tailwind CSS + shadcn/ui
- **Estado:** Zustand + React Query
- **Editor contenido:** TipTap / BlockNote

### Infraestructura IA
- **Embeddings:** OpenAI text-embedding-3-small
- **Vector DB:** Pinecone / Supabase pgvector
- **LLM:** OpenRouter (flexibilidad de modelos)
- **RAG Framework:** LangChain / LlamaIndex

### DevOps
- **Hosting:** Laravel Forge + DigitalOcean
- **CI/CD:** GitHub Actions
- **Monitoreo:** Laravel Telescope + Sentry

## Modelos de Datos Principales

```
users
├── id, email, password, role (instructor|student|admin)
├── profile: name, avatar, bio
└── preferences: learning_style, notifications

instructors
├── user_id
├── expertise_areas[]
├── verification_status
└── stripe_account_id

knowledge_bases (repositorios de conocimiento)
├── instructor_id
├── title, description, slug
├── status (draft|published|archived)
├── settings: public_access, pricing_model
└── metadata: total_chunks, last_indexed_at

content_chunks (fragmentos vectorizados)
├── knowledge_base_id
├── content (texto original)
├── embedding_vector
├── metadata: source_type, page_num, section
└── context_window

courses (organización jerárquica)
├── knowledge_base_id
├── title, description, level
├── structure: modules[] → lessons[]
└── settings: self_paced, scheduled

subscriptions
├── student_id
├── knowledge_base_id | course_id
├── status (active|cancelled|expired)
├── current_period_ends_at
└── payment_provider_data

conversations (interacciones IA)
├── student_id
├── knowledge_base_id
├── messages[]: {role, content, tokens_used}
├── context_retrieval: chunks_referenced[]
└── session_analytics: engagement_score

learning_sessions
├── student_id
├── content_reference
├── progress_data
├── quiz_results[]
└── time_spent
```

## Componentes del Sistema

### 1. Módulo de Autenticación
- Registro/login multi-rol
- OAuth (Google, Microsoft para instituciones)
- JWT con refresh tokens
- Verificación de instructores

### 2. Gestor de Contenido (Instructor)
- Editor WYSIWYG con bloques enriquecidos
- Upload de documentos (PDF, DOCX) → extracción automática
- Organización jerárquica: Base → Módulos → Lecciones
- Preview de cómo verá el estudiante

### 3. Motor de Indexación IA
- Procesamiento asíncrono de documentos
- Chunking inteligente (por párrafos/secciones)
- Generación de embeddings
- Almacenamiento en vector DB
- Actualización incremental

### 4. Experiencia del Estudiante
- Dashboard personalizado
- Explorador de contenido (búsqueda semántica)
- Chat IA contextual:
  - Modo "Tutor": pregunta-respuesta
  - Modo "Quiz": evaluación interactiva
  - Modo "Resumen": síntesis personalizada
  - Modo "Storytelling": narrativa inmersiva
- Configuración de estilo de aprendizaje

### 5. Sistema de Pagos
- Stripe Connect (marketplace)
- Suscripciones recurrentes
- Split de pagos (plataforma + instructor)
- Facturación automática

### 6. Analytics
- Engagement por contenido
- Métricas de aprendizaje
- Rendimiento del sistema IA
- Ingresos y conversiones

## Flujos Principales

### Flujo 1: Instructor crea contenido
```
Upload documento → Extracción texto → Chunking → 
Embeddings → Vector DB → Publicación → Disponible para estudiantes
```

### Flujo 2: Estudiante aprende con IA
```
Selecciona tema → Recupera chunks relevantes (RAG) → 
Genera respuesta contextual → Interacción conversacional → 
Guarda progreso
```

### Flujo 3: Suscripción
```
Estudiante elige instructor → Selecciona plan → 
Checkout Stripe → Activación inmediata → Acceso al contenido
```

## Consideraciones de Seguridad
- Encriptación datos sensibles en reposo
- Rate limiting en APIs de IA
- Validación de contenido subido
- Auditoría de accesos
- Cumplimiento GDPR/FERPA (datos educativos)

## Roadmap Técnico

### Fase 1 - MVP (2-3 meses)
- [ ] Auth básico + perfiles
- [ ] Upload y visualización de documentos
- [ ] Chat IA simple (un modelo)
- [ ] Suscripciones básicas

### Fase 2 - Beta (2 meses adicionales)
- [ ] Editor de contenido enriquecido
- [ ] Múltiples modos de IA
- [ ] Analytics dashboard
- [ ] API pública para integraciones

### Fase 3 - Scale (continuo)
- [ ] White-label para instituciones
- [ ] Mobile apps
- [ ] Features avanzadas de IA (agentes especializados)

---
*Documento creado por el Agente Arquitecto*
*Fecha: 2025-02-25*
