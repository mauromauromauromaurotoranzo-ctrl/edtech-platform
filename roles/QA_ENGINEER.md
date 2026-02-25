# Rol: QA Engineer (Quality Assurance)

## Responsabilidades Principales
- Diseñar y ejecutar casos de prueba
- Testing manual y automatizado
- Reporte y seguimiento de bugs
- Validación de requerimientos
- Performance testing básico

## Tipos de Testing

### 1. Functional Testing
- Unit tests (PHPUnit, Jest)
- Integration tests
- API tests (Postman, Pest)
- E2E tests (Playwright)

### 2. UI/UX Testing
- Responsive design (móvil, tablet, desktop)
- Accesibilidad (WCAG 2.1)
- Cross-browser testing

### 3. Performance Testing
- Lighthouse scores
- Load testing (k6)
- Database query optimization

### 4. Security Testing
- SQL injection attempts
- XSS prevention
- Auth/authorization flows

## Herramientas
| Uso | Herramienta |
|-----|-------------|
| Test Management | Notion / Linear |
| API Testing | Postman, Insomnia |
| E2E | Playwright |
| Performance | Lighthouse, k6 |
| Bug Tracking | GitHub Issues |

## Template de Bug Report
```markdown
**Título:** Breve descripción del bug

**Pasos para reproducir:**
1. Ir a...
2. Click en...
3. ...

**Comportamiento esperado:**
Debería...

**Comportamiento actual:**
Está...

**Screenshots/Videos:**
[adjuntar]

**Entorno:**
- OS: Windows/Mac/Linux
- Navegador: Chrome/Firefox/Safari
- Versión: X.X.X

**Severidad:** Critical/High/Medium/Low
```

## Definition of Done (DoD)
Un feature está listo cuando:
- [ ] Tests unitarios pasan (>80% coverage)
- [ ] Tests de integración pasan
- [ ] QA validó funcionalmente
- [ ] No hay bugs críticos o altos abiertos
- [ ] Performance es aceptable
- [ ] Documentación actualizada
