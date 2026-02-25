# EdTech Platform - Proyecto Educativo con IA

**Emprendedor:** Mauro Toranzo  
**Fecha inicio:** Febrero 2025  
**Estado:** Definiendo arquitectura y MVP

---

## 🎯 Visión

Plataforma educativa donde instructores entrenan conocimiento estructurado y estudiantes aprenden mediante IA conversacional adaptativa.

> "La nueva era de los libros" — aprendizaje interactivo, divertido y personalizable.

---

## 📁 Estructura del Proyecto

```
edtech-platform/
├── ARCHITECTURE.md          # Arquitectura técnica completa
├── README.md                # Este archivo
├── agents/                  # Perfiles de agentes especializados
│   ├── BACKEND_DEV.md
│   ├── FRONTEND_DEV.md
│   ├── IA_SPECIALIST.md
│   ├── PRODUCT_MANAGER.md
│   └── DEVOPS.md
├── backend/                 # Código Laravel (próximamente)
├── frontend/                # Código Next.js (próximamente)
├── docs/                    # Documentación adicional
└── memory/                  # Decisiones y aprendizajes
```

---

## 🚀 Estado Actual

### ✅ Completado
- [x] Definición de concepto y modelo de negocio
- [x] Arquitectura técnica inicial
- [x] Stack tecnológico definido
- [x] Modelos de datos diseñados
- [x] Agentes especializados configurados

### 🔄 En Progreso
- [ ] Definir features exactas del MVP
- [ ] Elegir entre Laravel+React o Next.js full-stack
- [ ] Crear repositorio Git
- [ ] Setup inicial de proyecto

### ⏳ Pendiente
- [ ] Desarrollo backend (APIs, auth, DB)
- [ ] Desarrollo frontend (UI, chat IA)
- [ ] Integración RAG y embeddings
- [ ] Sistema de pagos
- [ ] Testing y QA
- [ ] Deploy a producción

---

## 👥 Agentes Disponibles

Cada agente tiene un perfil detallado en `/agents/`:

| Agente | Archivo | Uso |
|--------|---------|-----|
| 🏗️ Arquitecto | `ARCHITECTURE.md` | Decisiones técnicas de alto nivel |
| ⚙️ Backend Dev | `BACKEND_DEV.md` | APIs, base de datos, lógica |
| 🎨 Frontend Dev | `FRONTEND_DEV.md` | UI/UX, componentes React |
| 🤖 IA Specialist | `IA_SPECIALIST.md` | Embeddings, RAG, prompts |
| 📊 Product Manager | `PRODUCT_MANAGER.md` | Roadmap, prioridades, OKRs |
| 🚀 DevOps | `DEVOPS.md` | Infra, deploy, monitoreo |

---

## 💬 Cómo Usar los Agentes

Para consultar a un agente específico, decime:

> "@BackendDev, ¿cómo implemento la autenticación JWT?"

O simplemente preguntame y yo invoco al agente adecuado según el contexto.

---

## ❓ Preguntas Clave por Responder

1. **¿Un instructor = un curso, o puede tener varios?**
2. **¿El estudiante paga por curso o suscripción mensual general?**
3. **¿La interacción IA es tipo chat o algo más visual/interactivo?**

---

## 🛠️ Stack Tecnológico

| Capa | Tecnología |
|------|------------|
| Backend | Laravel 11, PHP 8.3 |
| Frontend | Next.js 14, TypeScript, Tailwind |
| Base de datos | PostgreSQL 16, Redis |
| Vector DB | Pinecone / pgvector |
| IA | OpenRouter, OpenAI Embeddings |
| Hosting | Laravel Forge + DigitalOcean |
| CI/CD | GitHub Actions |

---

## 📞 Contacto

- **Telegram:** @Elmaquinas99_bot
- **Desarrollador:** Mauro Toranzo

---

*Última actualización: 2025-02-25*
