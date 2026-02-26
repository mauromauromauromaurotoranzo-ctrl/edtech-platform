/**
 * Notification Service for Telegram
 * Sends immediate notifications for project events
 */

const CHAT_ID = '8634190701';
const BOT_TOKEN = process.env.TELEGRAM_BOT_TOKEN || '7730409348:AAGi-BkS6XqzfCFxXA2h7vWIeQ0T3G4L5QE';

type NotificationType = 'success' | 'error' | 'warning' | 'info' | 'task' | 'deploy';

interface NotificationPayload {
  title: string;
  message: string;
  type?: NotificationType;
  project: 'edtech' | 'startup';
}

const emojis: Record<NotificationType, string> = {
  success: '✅',
  error: '❌',
  warning: '⚠️',
  info: 'ℹ️',
  task: '📋',
  deploy: '🚀',
};

export async function sendTelegramNotification({
  title,
  message,
  type = 'info',
  project,
}: NotificationPayload): Promise<void> {
  const emoji = emojis[type];
  const timestamp = new Date().toLocaleString('es-ES', {
    timeZone: 'Asia/Shanghai',
    hour12: false,
  });

  const projectTag = project === 'edtech' ? '🔷 EdTech' : '🚀 Startup';

  const fullMessage = `${emoji} *${title}*

${projectTag}
${message}

⏰ _${timestamp}_`;

  try {
    const response = await fetch(
      `https://api.telegram.org/bot${BOT_TOKEN}/sendMessage`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          chat_id: CHAT_ID,
          text: fullMessage,
          parse_mode: 'Markdown',
          disable_notification: type === 'info',
        }),
      }
    );

    if (!response.ok) {
      console.error('Failed to send Telegram notification:', await response.text());
    }
  } catch (error) {
    console.error('Error sending notification:', error);
  }
}

// Helper functions for common notifications
export const notify = {
  // EdTech Project
  edtech: {
    deploySuccess: (details: string) =>
      sendTelegramNotification({
        title: 'Deploy Exitoso',
        message: details,
        type: 'deploy',
        project: 'edtech',
      }),
    
    deployFailed: (error: string) =>
      sendTelegramNotification({
        title: 'Deploy Fallido',
        message: error,
        type: 'error',
        project: 'edtech',
      }),
    
    taskCompleted: (taskName: string) =>
      sendTelegramNotification({
        title: 'Tarea Completada',
        message: taskName,
        type: 'success',
        project: 'edtech',
      }),
    
    testFailed: (testName: string) =>
      sendTelegramNotification({
        title: 'Tests Fallidos',
        message: testName,
        type: 'error',
        project: 'edtech',
      }),
    
    backupCompleted: () =>
      sendTelegramNotification({
        title: 'Backup Realizado',
        message: 'Backup de base de datos completado exitosamente',
        type: 'success',
        project: 'edtech',
      }),
  },

  // Startup Project
  startup: {
    taskAssigned: (task: string, deadline?: string) =>
      sendTelegramNotification({
        title: 'Nueva Tarea Asignada',
        message: `${task}${deadline ? `\n📅 Deadline: ${deadline}` : ''}`,
        type: 'task',
        project: 'startup',
      }),
    
    meetingReminder: (meeting: string, time: string) =>
      sendTelegramNotification({
        title: 'Recordatorio de Reunión',
        message: `${meeting}\n🕐 Hora: ${time}`,
        type: 'warning',
        project: 'startup',
      }),
    
    milestoneReached: (milestone: string) =>
      sendTelegramNotification({
        title: '¡Hit Alcanzado! 🎉',
        message: milestone,
        type: 'success',
        project: 'startup',
      }),
    
    documentShared: (docName: string) =>
      sendTelegramNotification({
        title: 'Documento Compartido',
        message: docName,
        type: 'info',
        project: 'startup',
      }),
  },
};
