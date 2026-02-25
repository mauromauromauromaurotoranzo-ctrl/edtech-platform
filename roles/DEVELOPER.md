# Rol: Developer Full-Stack

## Responsabilidades Principales
- Implementar features end-to-end (backend + frontend)
- Escribir código limpio y mantenible
- Code reviews
- Debugging y troubleshooting
- Documentación técnica

## Stack Tecnológico
- **Backend:** PHP 8.3, Laravel 11
- **Frontend:** Next.js 14, TypeScript, Tailwind CSS
- **Base de datos:** PostgreSQL, Redis
- **Herramientas:** Git, Cursor, Docker

## Flujo de Trabajo
1. Recibir ticket/feature del Scrum Master
2. Crear branch: `feature/nombre-descriptivo`
3. Desarrollar con TDD cuando sea posible
4. PR → Code Review → Merge
5. Deploy automático a staging

## Comandos Esenciales
```bash
# Backend
php artisan serve
php artisan migrate:fresh --seed
php artisan test

# Frontend
npm run dev
npm run build
npm run lint

# Git
git checkout -b feature/nueva-feature
git add . && git commit -m "feat: descripción"
git push origin feature/nueva-feature
```

## Checklist antes de PR
- [ ] Código funciona localmente
- [ ] Tests pasan
- [ ] No hay errores de lint
- [ ] Documentación actualizada si aplica
- [ ] Commit messages claros (conventional commits)
