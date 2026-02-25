# Plan de Trabajo - EdTech Platform
## Ejecución por Bloques

### 📋 BLOQUE 1: Estructura Backend Hexagonal
**Estado:** ⏳ Pendiente  
**Duración estimada:** 2-3 horas  
**Dependencias:** Ninguna

#### Tareas:
- [ ] Crear directorios de arquitectura hexagonal
- [ ] Implementar entidades del Domain (Student, Instructor, KnowledgeBase, Course, Subscription, Conversation)
- [ ] Crear ValueObjects (Email, Money, VoiceSettings, LearningPreferences)
- [ ] Definir RepositoryInterfaces
- [ ] Crear migraciones Laravel
- [ ] Implementar Repositorios Eloquent

#### Entregable:
Estructura base del backend lista para usar en `/backend/`

---

### 📋 BLOQUE 2: Adaptadores de Mensajería
**Estado:** ⏳ Pendiente  
**Duración estimada:** 2-3 horas  
**Dependencias:** Bloque 1

#### Tareas:
- [ ] Adaptador WhatsApp Business API
- [ ] Adaptador Telegram Bot (ya configurado)
- [ ] Adaptador Email (SMTP)
- [ ] Sistema de preferencias de canal por estudiante
- [ ] Fallback entre canales

#### Entregable:
Sistema de notificaciones multi-canal funcional

---

### 📋 BLOQUE 3: Integración IA + Voice Cloning
**Estado:** ⏳ Pendiente  
**Duración estimada:** 3-4 horas  
**Dependencias:** Bloque 1

#### Tareas:
- [ ] Adaptador OpenRouter (LLMs)
- [ ] Adaptador ElevenLabs (Voice Cloning)
- [ ] Servicio de generación de respuestas con contexto RAG
- [ ] Servicio de síntesis de voz con voz del instructor
- [ ] Almacenamiento de voice_ids

#### Entregable:
IA conversacional con voz personalizada lista

---

### 📋 BLOQUE 4: Sistema de Desafíos Diarios
**Estado:** ⏳ Pendiente  
**Duración estimada:** 2-3 horas  
**Dependencias:** Bloques 1, 2, 3

#### Tareas:
- [ ] Entity DailyChallenge con tipos (QUIZ, PUZZLE, SCENARIO, FLASHCARD)
- [ ] Generador de desafíos con IA
- [ ] Scheduler para envío diario (Laravel Scheduler)
- [ ] Evaluador de respuestas
- [ ] Sistema de puntos/recompensas

#### Entregable:
Desafíos automáticos funcionando

---

### 📋 BLOQUE 5: Recordatorios Inteligentes
**Estado:** ⏳ Pendiente  
**Duración estimada:** 2 horas  
**Dependencias:** Bloques 1, 2

#### Tareas:
- [ ] Algoritmo Spaced Repetition (SM-2)
- [ ] Detección de inactividad
- [ ] Recordatorios de examen
- [ ] Configuración de frecuencia por estudiante

#### Entregable:
Sistema de recordatorios inteligentes activo

---

### 📋 BLOQUE 6: Frontend Next.js
**Estado:** ⏳ Pendiente  
**Duración estimada:** 4-5 horas  
**Dependencias:** Bloque 1

#### Tareas:
- [ ] Setup Next.js 14 + TypeScript + Tailwind
- [ ] Auth screens (login/register)
- [ ] Dashboard instructor
- [ ] Dashboard estudiante
- [ ] Chat interactivo con IA
- [ ] Visualizador de contenido multimedia

#### Entregable:
Frontend funcional conectado al backend

---

### 📋 BLOQUE 7: Pasarela de Pagos (Stripe)
**Estado:** ⏳ Pendiente  
**Duración estimada:** 2-3 horas  
**Dependencias:** Bloque 1

#### Tareas:
- [ ] Integración Stripe Connect
- [ ] Suscripciones recurrentes
- [ ] Split de pagos (plataforma/instructor)
- [ ] Webhooks para eventos de pago
- [ ] Facturación

#### Entregable:
Sistema de pagos completo

---

### 📋 BLOQUE 8: Testing + QA
**Estado:** ⏳ Pendiente  
**Duración estimada:** 3-4 horas  
**Dependencias:** Todos los bloques anteriores

#### Tareas:
- [ ] Tests unitarios (PHPUnit, Jest)
- [ ] Tests de integración
- [ ] E2E tests (Playwright)
- [ ] Performance testing
- [ ] Security audit básico

#### Entregable:
Cobertura de tests > 80%

---

### 📋 BLOQUE 9: DevOps + Deploy
**Estado:** ⏳ Pendiente  
**Duración estimada:** 2-3 horas  
**Dependencias:** Todos los bloques anteriores

#### Tareas:
- [ ] Dockerización
- [ ] CI/CD GitHub Actions
- [ ] Setup servidor (Laravel Forge/DigitalOcean)
- [ ] SSL, backups, monitoreo
- [ ] Deploy a producción

#### Entregable:
Producción live y estable

---

## 🚀 Instrucciones de Uso

1. **Ejecutar bloque por bloque** secuencialmente
2. **Cada bloque debe completarse antes de pasar al siguiente**
3. **Al finalizar cada bloque, actualizar este archivo marcando ✅**
4. **Reportar progreso y cualquier bloqueo encontrado**

## 📊 Progreso General
- [ ] Bloque 1
- [ ] Bloque 2
- [ ] Bloque 3
- [ ] Bloque 4
- [ ] Bloque 5
- [ ] Bloque 6
- [ ] Bloque 7
- [ ] Bloque 8
- [ ] Bloque 9

**Progreso: 0/9 bloques completados (0%)**
