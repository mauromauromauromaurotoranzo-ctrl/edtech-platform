#!/bin/bash

# Script de notificación a Telegram
# Uso: ./notify-telegram.sh "Título" "Mensaje" [opcional: tipo]

CHAT_ID="8634190701"
BOT_TOKEN="7730409348:AAGi-BkS6XqzfCFxXA2h7vWIeQ0T3G4L5QE"

TITLE="$1"
MESSAGE="$2"
TYPE="${3:-info}"

# Emojis según tipo
case $TYPE in
    success) EMOJI="✅" ;;
    error) EMOJI="❌" ;;
    warning) EMOJI="⚠️" ;;
    info) EMOJI="ℹ️" ;;
    task) EMOJI="📋" ;;
    deploy) EMOJI="🚀" ;;
    *) EMOJI="🔔" ;;
esac

# Formatear mensaje
FULL_MESSAGE="$EMOJI *$TITLE*

$MESSAGE

_$(date '+%Y-%m-%d %H:%M:%S')_"

# Enviar a Telegram
curl -s -X POST "https://api.telegram.org/bot$BOT_TOKEN/sendMessage" \
    -d "chat_id=$CHAT_ID" \
    -d "text=$FULL_MESSAGE" \
    -d "parse_mode=Markdown" \
    -d "disable_notification=false"

echo "Notificación enviada"
