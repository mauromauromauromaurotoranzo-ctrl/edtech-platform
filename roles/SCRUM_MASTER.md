# Rol: Scrum Master / Agile Coach

## Responsabilidades Principales
- Facilitar ceremonias Scrum
- Remover impedimentos
- Asegurar flujo de trabajo (Kanban/Sprint)
- Proteger al equipo de distracciones
- Métricas de equipo (velocity, burndown)
- Mejora continua (retrospectives)

## Ceremonias

### Daily Standup (15 min)
Cada uno responde:
1. ¿Qué hice ayer?
2. ¿Qué haré hoy?
3. ¿Tengo algún bloqueo?

### Sprint Planning (2-4 horas)
- Definir objetivo del sprint
- Estimar stories (story points)
- Comprometerse al backlog del sprint

### Sprint Review (1 hora)
- Demo de lo completado
- Feedback de stakeholders
- Actualizar roadmap

### Retrospective (1 hora)
- ¿Qué funcionó bien?
- ¿Qué podemos mejorar?
- Acciones concretas para el próximo sprint

## Herramientas de Gestión
| Propósito | Herramienta |
|-----------|-------------|
| Project Management | Linear, Jira, o GitHub Projects |
| Comunicación | Telegram (async), Discord (sync) |
| Documentación | Notion, GitHub Wiki |
| Time tracking | Opcional (Clockify) |

## Flujo de Trabajo (GitHub Flow)
```
Backlog → To Do → In Progress → Code Review → QA → Done
```

### Estados
- **Backlog:** Ideas y features futuras
- **To Do:** Priorizado para el sprint actual
- **In Progress:** Alguien trabajando activamente
- **Code Review:** PR abierto, esperando review
- **QA:** En testing
- **Done:** En producción

## Estimación (Story Points)
| Puntos | Tiempo aproximado | Complejidad |
|--------|-------------------|-------------|
| 1 | 1-2 horas | Trivial |
| 2 | 2-4 horas | Simple |
| 3 | 4-8 horas | Media |
| 5 | 1-2 días | Compleja |
| 8 | 2-3 días | Muy compleja |
| 13+ | Dividir en tareas más chicas | Demasiado grande |

## Métricas a Trackear
- **Velocity:** Story points completados por sprint
- **Cycle time:** Tiempo desde "In Progress" hasta "Done"
- **Bug escape rate:** Bugs encontrados en prod vs QA
- **Team happiness:** Encuesta anónima periódica

## Anti-patrones a Evitar
- ❌ Micromanagement
- ❌ Cambios de scope durante el sprint
- ❌ Multitasking excesivo
- ❌ Reuniones sin agenda clara
- ❌ Blame culture

## Comunicación Asíncrona (Telegram)
- Updates de progreso: #update
- Bloqueos que necesitan ayuda: #blocker
- Dudas técnicas: #question
- Celebrar wins: #win
