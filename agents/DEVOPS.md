# Agente DevOps - EdTech Platform

## Rol
Infraestructura, deployment y operaciones.

## Responsabilidades
- Setup de servidores y hosting
- CI/CD pipelines
- Monitoreo y alertas
- Backup y disaster recovery
- Seguridad y compliance
- Optimización de costos cloud

## Infraestructura Propuesta

### Producción
```
Laravel Forge (DigitalOcean)
├── App Server: 2 vCPU / 4GB RAM (escalable)
├── Database: Managed PostgreSQL
├── Redis: Managed Redis
├── Queue Workers: Horizon en servidor separado
└── CDN: CloudFlare
```

### Staging
- Mismo stack que producción pero menor tamaño
- Datos anonimizados

## CI/CD Pipeline (GitHub Actions)

```yaml
# .github/workflows/deploy.yml
name: Deploy
on:
  push:
    branches: [main]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - name: Install dependencies
        run: composer install --no-dev
      - name: Run tests
        run: php artisan test
  
  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Forge
        run: curl ${{ secrets.FORGE_DEPLOY_WEBHOOK }}
```

## Tareas Inmediatas
1. [ ] Crear cuenta Laravel Forge
2. [ ] Configurar servidor DigitalOcean
3. [ ] Setup SSL con Let's Encrypt
4. [ ] Configurar backups automáticos (DB + Storage)
5. [ ] Setup Sentry para error tracking
6. [ ] Configurar Telescope (solo staging/local)

## Checklist de Seguridad
- [ ] HTTPS forzado en todas las rutas
- [ ] Headers de seguridad (HSTS, CSP)
- [ ] Rate limiting en APIs
- [ ] Sanitización de inputs
- [ ] Variables sensibles en .env (no en repo)
- [ ] Acceso SSH solo por key
- [ ] Firewall configurado (solo puertos necesarios)

## Costos Estimados Mensuales (MVP)
| Servicio | Costo |
|----------|-------|
| DigitalOcean Droplet | $24 |
| Managed PostgreSQL | $15 |
| Redis Managed | $15 |
| S3/Spaces Storage | $5-10 |
| CloudFlare Pro | $20 |
| Sentry | $0 (plan gratuito) |
| **Total** | **~$80-90/mes** |
