# Environment Variables

## Required

### Database
| Variable | Description | Example |
|----------|-------------|---------|
| `DB_CONNECTION` | Database driver | `pgsql` |
| `DB_HOST` | Database host | `db` |
| `DB_PORT` | Database port | `5432` |
| `DB_DATABASE` | Database name | `edtech` |
| `DB_USERNAME` | Database user | `postgres` |
| `DB_PASSWORD` | Database password | `secret` |

### API Keys
| Variable | Description | Get from |
|----------|-------------|----------|
| `OPENROUTER_API_KEY` | LLM access | openrouter.ai |
| `ELEVENLABS_API_KEY` | Voice synthesis | elevenlabs.io |
| `OPENAI_API_KEY` | Embeddings | platform.openai.com |

## Optional

### Telegram Bot
| Variable | Description |
|----------|-------------|
| `TELEGRAM_BOT_TOKEN` | Bot token from @BotFather |

### WhatsApp Business
| Variable | Description |
|----------|-------------|
| `WHATSAPP_ACCESS_TOKEN` | Meta API token |
| `WHATSAPP_PHONE_NUMBER_ID` | Phone number ID |

### Email (SMTP)
| Variable | Description |
|----------|-------------|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | SMTP host |
| `MAIL_PORT` | SMTP port |
| `MAIL_USERNAME` | SMTP user |
| `MAIL_PASSWORD` | SMTP password |

## Development

```bash
# Copy example
cp backend/.env.example backend/.env

# Generate app key
php artisan key:generate
```
